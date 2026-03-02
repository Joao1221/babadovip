<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\FofocaRapidaModel;

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
        (new FofocaRapidaModel())->delete((int) $id);
        Flash::set('success', 'Fofoca removida.');
        redirect('/admin/fofocas');
    }

    private function save(?int $id): void
    {
        $model = new FofocaRapidaModel();
        if ($id && !$model->findById($id)) {
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

        if ($id) {
            $model->update($id, $data);
            Flash::set('success', 'Fofoca atualizada.');
            redirect('/admin/fofocas/' . $id . '/editar');
        }

        $newId = $model->create($data);
        Flash::set('success', 'Fofoca #' . $newId . ' criada com sucesso. Voce ja pode cadastrar a proxima.');
        redirect('/admin/fofocas/nova');
    }
}
