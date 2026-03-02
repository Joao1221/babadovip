<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\CategoryModel;
use App\Models\HomeCardModel;
use App\Models\HomeSectionModel;
use App\Models\PostModel;

final class HomeBuilderController extends BaseController
{
    public function index(): void
    {
        Auth::requireAdmin();
        $sections = (new HomeSectionModel())->all();
        $cardsModel = new HomeCardModel();
        $this->render('admin/home_builder/index', [
            'title' => 'Home Builder',
            'sections' => array_map(static function (array $sec) use ($cardsModel): array {
                $sec['cards'] = $cardsModel->listBySection((int) $sec['id']);
                return $sec;
            }, $sections),
            'categories' => (new CategoryModel())->allActive(),
            'recentPosts' => (new PostModel())->latest(200),
        ], 'admin');
    }

    public function updateSection(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $mode = $_POST['modo'] ?? 'auto';
        if (!in_array($mode, ['auto', 'manual', 'misto'], true)) {
            $mode = 'auto';
        }
        $layout = $_POST['layout'] ?? 'grid';
        if (!in_array($layout, ['grid', 'lista', 'mosaico'], true)) {
            $layout = 'grid';
        }
        $limiteCards = max(1, (int) ($_POST['limite_cards'] ?? 6));
        $itensPorPagina = max(1, (int) ($_POST['itens_por_pagina'] ?? $limiteCards));
        (new HomeSectionModel())->updateConfig((int) $id, [
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0) ?: null,
            'modo' => $mode,
            'layout' => $layout,
            'limite_cards' => $limiteCards,
            'itens_por_pagina' => $itensPorPagina,
        ]);
        Flash::set('success', 'Seção atualizada.');
        redirect('/admin/home-builder');
    }

    public function upsertCard(string $sectionId, string $position): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $postId = (int) ($_POST['post_id'] ?? 0);
        if ($postId <= 0) {
            Flash::set('danger', 'Selecione uma matéria válida.');
            redirect('/admin/home-builder');
        }
        (new HomeCardModel())->upsertCard((int) $sectionId, (int) $position, $postId, 1);
        Flash::set('success', 'Card atualizado.');
        redirect('/admin/home-builder');
    }

    public function removeCard(string $sectionId, string $position): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        (new HomeCardModel())->removeCard((int) $sectionId, (int) $position);
        Flash::set('success', 'Card removido.');
        redirect('/admin/home-builder');
    }
}
