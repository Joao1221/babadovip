<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\CategoryModel;
use App\Models\FofocaRapidaModel;
use App\Models\PostModel;

final class FofocaRapidaController extends BaseController
{
    public function index(): void
    {
        Auth::requireAdmin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $model = new FofocaRapidaModel();
        $items = $model->listAdmin($limit, $offset);
        $count = $model->countAdmin();
        $pages = (int) max(1, ceil($count / $limit));

        $this->render('admin/fofocas/index', [
            'title' => 'Fofocas Rapidas - Admin',
            'items' => $items,
            'page' => $page,
            'pages' => $pages,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $this->render('admin/fofocas/form', [
            'title' => 'Nova Fofoca Rapida',
            'item' => null,
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $this->save(null);
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();
        $item = (new FofocaRapidaModel())->findById((int) $id);
        if (!$item) {
            http_response_code(404);
            exit('Fofoca nao encontrada.');
        }
        $this->render('admin/fofocas/form', [
            'title' => 'Editar Fofoca Rapida',
            'item' => $item,
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $this->save((int) $id);
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $model = new FofocaRapidaModel();
        $item = $model->findById((int) $id);
        if ($item && !empty($item['post_id'])) {
            (new PostModel())->delete((int) $item['post_id']);
        }
        $model->delete((int) $id);
        Flash::set('success', 'Fofoca removida.');
        redirect('/admin/fofocas');
    }

    private function save(?int $id): void
    {
        $model = new FofocaRapidaModel();
        $current = $id ? $model->findById($id) : null;
        if ($id && !$current) {
            Flash::set('danger', 'Fofoca nao encontrada.');
            redirect('/admin/fofocas');
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $subtitulo = trim((string) ($_POST['subtitulo'] ?? ''));
        $publicadoEm = trim((string) ($_POST['publicado_em'] ?? ''));
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($titulo === '') {
            Flash::set('danger', 'Titulo e obrigatorio.');
            redirect($id ? '/admin/fofocas/' . $id . '/editar' : '/admin/fofocas/nova');
        }

        $data = [
            'titulo' => mb_substr($titulo, 0, 220),
            'subtitulo' => $subtitulo !== '' ? mb_substr($subtitulo, 0, 500) : null,
            'ativo' => $ativo,
            'publicado_em' => $publicadoEm !== '' ? $publicadoEm : now(),
        ];
        $data['post_id'] = $this->saveLinkedPost($data, (int) ($current['post_id'] ?? 0));

        if ($id) {
            $model->update($id, $data);
            Flash::set('success', 'Fofoca atualizada.');
            redirect('/admin/fofocas/' . $id . '/editar');
        }

        $newId = $model->create($data);
        Flash::set('success', 'Fofoca #' . $newId . ' criada com sucesso. Voce ja pode cadastrar a proxima.');
        redirect('/admin/fofocas/nova');
    }

    private function saveLinkedPost(array $fofocaData, int $postId): int
    {
        $postModel = new PostModel();
        $categoryId = (new CategoryModel())->ensureFofocasRapidas();
        $adminId = (int) (Auth::admin()['id'] ?? 1);

        $title = (string) $fofocaData['titulo'];
        $subtitle = (string) ($fofocaData['subtitulo'] ?? '');
        $publishedAt = (string) $fofocaData['publicado_em'];
        $content = $this->buildFofocaContent($title, $subtitle);

        if ($postId > 0) {
            $existingPost = $postModel->findById($postId);
            if ($existingPost) {
                $postModel->update($postId, [
                    'categoria_id' => $categoryId,
                    'autor_admin_id' => $adminId,
                    'titulo' => $title,
                    'subtitulo' => $subtitle !== '' ? $subtitle : null,
                    'slug' => $postModel->generateUniqueSlug($title, $postId),
                    'conteudo_html' => $content,
                    'status' => 'published',
                    'publicado_em' => $publishedAt,
                    'is_breaking' => 0,
                    'is_exclusivo' => 0,
                    'is_vip' => 0,
                    'verificacao' => 'rumor',
                    'imagem_capa' => $existingPost['imagem_capa'],
                    'tags' => $existingPost['tags'],
                    'tempo_leitura' => 1,
                    'event_data' => null,
                    'event_hora' => null,
                    'event_local' => null,
                    'event_bairro_cidade' => null,
                    'overlay_titulo_cor' => $existingPost['overlay_titulo_cor'] ?? '#FFFFFF',
                ]);
                return $postId;
            }
        }

        return $postModel->create([
            'categoria_id' => $categoryId,
            'autor_admin_id' => $adminId,
            'titulo' => $title,
            'subtitulo' => $subtitle !== '' ? $subtitle : null,
            'slug' => $postModel->generateUniqueSlug($title),
            'conteudo_html' => $content,
            'status' => 'published',
            'publicado_em' => $publishedAt,
            'is_breaking' => 0,
            'is_exclusivo' => 0,
            'is_vip' => 0,
            'verificacao' => 'rumor',
            'imagem_capa' => null,
            'tags' => null,
            'tempo_leitura' => 1,
            'event_data' => null,
            'event_hora' => null,
            'event_local' => null,
            'event_bairro_cidade' => null,
            'overlay_titulo_cor' => '#FFFFFF',
        ]);
    }

    private function buildFofocaContent(string $title, string $subtitle): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeSubtitle = htmlspecialchars($subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if ($safeSubtitle === '') {
            return '<p>' . $safeTitle . '</p>';
        }

        return '<p>' . $safeTitle . '</p><p>' . nl2br($safeSubtitle) . '</p>';
    }
}
