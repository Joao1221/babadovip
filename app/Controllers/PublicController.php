<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\FofocaRapidaModel;
use App\Models\HomeCardModel;
use App\Models\HomeSectionModel;
use App\Models\PostModel;

final class PublicController extends BaseController
{
    public function home(): void
    {
        $sectionsModel = new HomeSectionModel();
        $cardsModel = new HomeCardModel();
        $postModel = new PostModel();
        $fofocaModel = new FofocaRapidaModel();

        $sections = $sectionsModel->all();
        $result = [];
        $gossipItems = array_map(static function (array $fofoca): array {
            $url = !empty($fofoca['post_slug'])
                ? url('/materia/' . $fofoca['post_slug'])
                : url('/fofocas#fofoca-' . (int) $fofoca['id']);
            return [
                'url' => $url,
                'time' => date('H:i', strtotime((string) $fofoca['publicado_em'])),
                'title' => $fofoca['titulo'],
                'subtitle' => $fofoca['subtitulo'] ?? '',
            ];
        }, $fofocaModel->listPublic(20));

        foreach ($sections as $section) {
            if (($section['slug'] ?? '') === 'ultimas') {
                continue;
            }
            $manualCards = $cardsModel->listBySection((int) $section['id'], true);
            $limit = max(1, (int) $section['limite_cards']);
            if (($section['slug'] ?? '') === 'sociedade-festas') {
                $limit = max($limit, 7);
            } elseif (($section['slug'] ?? '') === 'eventos-agenda') {
                $limit = max($limit, 9);
            }
            $mode = $section['modo'];
            $categoryId = (int) ($section['categoria_id'] ?? 0);

            $posts = [];
            $manualPostIds = array_map(static fn(array $c): int => (int) $c['post_id'], $manualCards);
            if ($mode === 'manual') {
                $posts = array_slice($manualCards, 0, $limit);
            } elseif ($mode === 'misto') {
                $fixedLimit = (($section['slug'] ?? '') === 'sociedade-festas') ? min(7, $limit) : min(3, $limit);
                $fixed = array_slice($manualCards, 0, $fixedLimit);
                $posts = $fixed;
                $need = $limit - count($fixed);
                if ($need > 0 && $categoryId > 0) {
                    $auto = $postModel->latestByCategory($categoryId, $need, $manualPostIds);
                    $posts = array_merge($posts, $auto);
                }
            } else {
                if ($section['slug'] === 'ultimas') {
                    $posts = $postModel->latest($limit);
                } elseif ($categoryId > 0) {
                    $posts = $postModel->latestByCategory($categoryId, $limit);
                }
            }

            $result[] = [
                'meta' => $section,
                'posts' => $posts,
            ];
        }

        $this->render('public/home', [
            'title' => 'BabadoVip - Portal',
            'sections' => $result,
            'gossipItems' => $gossipItems,
        ]);
    }

    public function fofocas(): void
    {
        $items = (new FofocaRapidaModel())->listPublic(200);
        $this->render('public/fofocas', [
            'title' => 'Fofocas - BabadoVip',
            'items' => $items,
        ]);
    }

    public function ultimas(): void
    {
        $section = (new HomeSectionModel())->findBySlug('ultimas');
        $cardsModel = new HomeCardModel();
        $postModel = new PostModel();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $displayLimit = max(1, (int) ($section['limite_cards'] ?? 12));
        $perPage = max(1, (int) ($section['itens_por_pagina'] ?? $displayLimit));

        $mode = $section['modo'] ?? 'auto';
        $layout = $section['layout'] ?? 'grid';
        $categoryId = (int) ($section['categoria_id'] ?? 0);
        $manualCardsRaw = $section ? $cardsModel->listBySection((int) $section['id'], true) : [];
        $manualCards = array_slice($manualCardsRaw, 0, $displayLimit);
        $manualPostIds = array_map(static fn(array $c): int => (int) $c['post_id'], $manualCards);
        $allPosts = [];

        if ($mode === 'manual') {
            $allPosts = $manualCards;
        } elseif ($mode === 'misto') {
            $fixed = array_slice($manualCards, 0, min(3, $displayLimit));
            $need = max(0, $displayLimit - count($fixed));
            $auto = $need > 0
                ? $postModel->latestPublicFiltered($categoryId > 0 ? $categoryId : null, $need, 0, $manualPostIds)
                : [];
            $allPosts = array_slice(array_merge($fixed, $auto), 0, $displayLimit);
        } else {
            $allPosts = $postModel->latestPublicFiltered($categoryId > 0 ? $categoryId : null, $displayLimit, 0);
        }

        $total = count($allPosts);
        $pages = (int) max(1, ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;
        $posts = array_slice($allPosts, $offset, $perPage);

        $this->render('public/ultimas', [
            'title' => 'Ultimas - BabadoVip',
            'posts' => $posts,
            'page' => $page,
            'pages' => $pages,
            'sectionLayout' => in_array($layout, ['grid', 'lista', 'mosaico'], true) ? $layout : 'grid',
            'limite' => $displayLimit,
            'itensPorPagina' => $perPage,
            'sectionMode' => in_array($mode, ['auto', 'manual', 'misto'], true) ? $mode : 'auto',
        ]);
    }

    public function category(string $slug): void
    {
        $category = (new CategoryModel())->findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Categoria nao encontrada']);
            return;
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $sort = ($_GET['sort'] ?? 'recentes') === 'mais_lidas' ? 'mais_lidas' : 'recentes';
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $postModel = new PostModel();
        $posts = $postModel->listByCategoryPublic((int) $category['id'], $sort, $limit, $offset);
        $total = $postModel->countByCategoryPublic((int) $category['id']);
        $pages = (int) max(1, ceil($total / $limit));

        $this->render('public/category', [
            'title' => $category['nome'] . ' - BabadoVip',
            'category' => $category,
            'posts' => $posts,
            'page' => $page,
            'pages' => $pages,
            'sort' => $sort,
        ]);
    }
}
