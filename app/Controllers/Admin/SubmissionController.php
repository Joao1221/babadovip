<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\PostPhotoModel;
use App\Models\SubmissionModel;
use App\Models\SubmissionPhotoModel;
use App\Services\AuditService;
use App\Services\UploadService;

final class SubmissionController extends BaseController
{
    public function index(): void
    {
        Auth::requireAdmin();
        $filters = [
            'status' => trim((string) ($_GET['status'] ?? '')),
            'categoria_id' => (int) ($_GET['categoria_id'] ?? 0),
            'q' => trim((string) ($_GET['q'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $model = new SubmissionModel();
        $items = $model->listAdmin($filters, $limit, $offset);
        $count = $model->countAdmin($filters);
        $pages = (int) max(1, ceil($count / $limit));

        $this->render('admin/submissions/index', [
            'title' => 'Envios dos Leitores',
            'items' => $items,
            'filters' => $filters,
            'categories' => (new CategoryModel())->allActive(),
            'page' => $page,
            'pages' => $pages,
        ], 'admin');
    }

    public function show(string $id): void
    {
        Auth::requireAdmin();
        $submission = (new SubmissionModel())->findById((int) $id);
        if (!$submission) {
            http_response_code(404);
            exit('Envio não encontrado.');
        }
        $this->render('admin/submissions/show', [
            'title' => 'Detalhe do Envio',
            'submission' => $submission,
            'photos' => (new SubmissionPhotoModel())->listBySubmission((int) $id),
            'categories' => (new CategoryModel())->allActive(),
        ], 'admin');
    }

    public function approve(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $submissionModel = new SubmissionModel();
        $photoModel = new SubmissionPhotoModel();
        $postModel = new PostModel();
        $postPhotoModel = new PostPhotoModel();

        $submission = $submissionModel->findById((int) $id);
        if (!$submission || $submission['status'] !== 'pendente') {
            Flash::set('danger', 'Envio inválido para aprovação.');
            redirect('/admin/submissions');
        }

        $publishMode = $_POST['publish_mode'] ?? 'draft';
        $status = $publishMode === 'published' ? 'published' : 'draft';

        $newPostId = $postModel->create([
            'categoria_id' => (int) ($submission['categoria_sugerida_id'] ?: 1),
            'autor_admin_id' => (int) (Auth::admin()['id'] ?? 1),
            'titulo' => $submission['titulo'],
            'subtitulo' => $submission['subtitulo'],
            'slug' => $postModel->generateUniqueSlug($submission['titulo']),
            'conteudo_html' => $this->sanitizeRichText((string) $submission['conteudo']),
            'status' => $status,
            'publicado_em' => $status === 'published' ? now() : null,
            'is_breaking' => 0,
            'is_exclusivo' => 0,
            'is_vip' => 0,
            'verificacao' => 'rumor',
            'imagem_capa' => null,
            'tags' => null,
            'tempo_leitura' => 3,
            'event_data' => null,
            'event_hora' => null,
            'event_local' => null,
            'event_bairro_cidade' => null,
            'overlay_titulo_cor' => '#FFFFFF',
        ]);

        $submissionPhotos = $photoModel->listBySubmission((int) $id);
        $postPhotos = [];
        foreach ($submissionPhotos as $i => $sp) {
            $postPhotos[] = [
                'arquivo' => $sp['arquivo'],
                'legenda' => $sp['legenda'],
                'ordem' => $sp['ordem'] ?? $i,
            ];
        }
        if ($postPhotos) {
            $postPhotoModel->replaceForPost($newPostId, $postPhotos);
            $post = $postModel->findById($newPostId);
            if ($post && !$post['imagem_capa']) {
                $post['imagem_capa'] = $postPhotos[0]['arquivo'];
                $postModel->update($newPostId, [
                    'categoria_id' => (int) $post['categoria_id'],
                    'autor_admin_id' => (int) (Auth::admin()['id'] ?? 1),
                    'titulo' => $post['titulo'],
                    'subtitulo' => $post['subtitulo'],
                    'slug' => $post['slug'],
                    'conteudo_html' => $post['conteudo_html'],
                    'status' => $post['status'],
                    'publicado_em' => $post['publicado_em'],
                    'is_breaking' => (int) $post['is_breaking'],
                    'is_exclusivo' => (int) $post['is_exclusivo'],
                    'is_vip' => (int) $post['is_vip'],
                    'verificacao' => $post['verificacao'],
                    'imagem_capa' => $post['imagem_capa'],
                    'tags' => $post['tags'],
                    'tempo_leitura' => (int) $post['tempo_leitura'],
                    'event_data' => $post['event_data'],
                    'event_hora' => $post['event_hora'],
                    'event_local' => $post['event_local'],
                    'event_bairro_cidade' => $post['event_bairro_cidade'],
                    'overlay_titulo_cor' => $post['overlay_titulo_cor'] ?? '#FFFFFF',
                ]);
            }
        }

        $submissionModel->markApproved((int) $id, (int) (Auth::admin()['id'] ?? 1), $newPostId);
        AuditService::log('submission_approved', ['submission_id' => (int) $id, 'post_id' => $newPostId]);
        Flash::set('success', 'Envio aprovado e convertido para post.');
        redirect('/admin/posts/' . $newPostId . '/editar');
    }

    public function reject(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        (new SubmissionModel())->markRejected(
            (int) $id,
            (int) (Auth::admin()['id'] ?? 1),
            trim((string) ($_POST['motivo_rejeicao'] ?? '')) ?: null
        );
        AuditService::log('submission_rejected', ['submission_id' => (int) $id]);
        Flash::set('success', 'Envio rejeitado.');
        redirect('/admin/submissions/' . $id);
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $model = new SubmissionModel();
        $submission = $model->findById((int) $id);
        if ($submission) {
            $dir = PUBLIC_PATH . '/uploads/submissions/' . date('Y/m', strtotime((string) $submission['criado_em'])) . '/sub-' . $submission['id'];
            (new UploadService())->deleteDirectory($dir);
            $model->delete((int) $id);
            AuditService::log('submission_deleted', ['submission_id' => (int) $id]);
        }
        Flash::set('success', 'Envio excluído.');
        redirect('/admin/submissions');
    }
}
