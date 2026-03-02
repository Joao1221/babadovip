<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Models\ContactMessageModel;

final class ContactMessageController extends BaseController
{
    public function index(): void
    {
        Auth::requireAdmin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $model = new ContactMessageModel();
        $items = $model->listAdmin($limit, $offset);
        $count = $model->countAdmin();
        $pages = (int) max(1, ceil($count / $limit));

        $this->render('admin/messages/index', [
            'title' => 'Mensagens de Contato',
            'items' => $items,
            'page' => $page,
            'pages' => $pages,
        ], 'admin');
    }

    public function show(string $id): void
    {
        Auth::requireAdmin();
        $model = new ContactMessageModel();
        $item = $model->findById((int) $id);
        if (!$item) {
            http_response_code(404);
            exit('Mensagem não encontrada.');
        }
        $model->markRead((int) $id);
        $this->render('admin/messages/show', [
            'title' => 'Mensagem #' . (int) $id,
            'item' => $item,
        ], 'admin');
    }
}
