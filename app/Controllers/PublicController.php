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
        $principalPostId = $this->resolvePrincipalPostId($sections, $cardsModel, $postModel);
        $globalSeenPostIds = [];
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
            $excludeIds = array_keys($globalSeenPostIds);
            if ($principalPostId > 0 && ($section['slug'] ?? '') !== 'sociedade-festas') {
                $excludeIds[] = $principalPostId;
            }

            $posts = $this->resolveSectionPosts($section, $cardsModel, $postModel, $excludeIds);
            $posts = $this->dedupeAndTrackPosts($posts, $globalSeenPostIds);

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

    private function resolvePrincipalPostId(array $sections, HomeCardModel $cardsModel, PostModel $postModel): int
    {
        foreach ($sections as $section) {
            if (($section['slug'] ?? '') !== 'sociedade-festas') {
                continue;
            }
            $posts = $this->resolveSectionPosts($section, $cardsModel, $postModel);
            return $this->extractPostId($posts[0] ?? null);
        }
        return 0;
    }

    private function resolveSectionPosts(
        array $section,
        HomeCardModel $cardsModel,
        PostModel $postModel,
        array $excludeIds = []
    ): array {
        $exclude = array_values(array_filter(array_map('intval', $excludeIds), static fn(int $id): bool => $id > 0));
        $excludeMap = [];
        foreach ($exclude as $id) {
            $excludeMap[$id] = true;
        }

        $manualCards = $cardsModel->listBySection((int) $section['id'], true);
        if ($excludeMap !== []) {
            $manualCards = array_values(array_filter($manualCards, function (array $card) use ($excludeMap): bool {
                $postId = (int) ($card['post_id'] ?? 0);
                return $postId <= 0 || !isset($excludeMap[$postId]);
            }));
        }

        $limit = max(1, (int) ($section['limite_cards'] ?? 1));
        $mode = $section['modo'] ?? 'auto';
        $categoryId = (int) ($section['categoria_id'] ?? 0);
        $manualPostIds = array_map(static fn(array $c): int => (int) $c['post_id'], $manualCards);
        $queryExcludeIds = array_values(array_unique(array_merge($manualPostIds, $exclude)));

        if ($mode === 'manual') {
            return array_slice($manualCards, 0, $limit);
        }
        if ($mode === 'misto') {
            $fixedLimit = (($section['slug'] ?? '') === 'sociedade-festas') ? min(7, $limit) : min(3, $limit);
            $fixed = array_slice($manualCards, 0, $fixedLimit);
            $posts = $fixed;
            $need = $limit - count($fixed);
            if ($need > 0 && $categoryId > 0) {
                $auto = $postModel->latestByCategory($categoryId, $need, $queryExcludeIds);
                $posts = array_merge($posts, $auto);
            }
            return $posts;
        }
        if ($categoryId > 0) {
            return $postModel->latestByCategory($categoryId, $limit, $exclude);
        }
        return [];
    }

    private function extractPostId(mixed $post): int
    {
        if (!is_array($post)) {
            return 0;
        }
        $postId = (int) ($post['post_id'] ?? 0);
        if ($postId > 0) {
            return $postId;
        }
        return (int) ($post['id'] ?? 0);
    }

    private function dedupeAndTrackPosts(array $posts, array &$globalSeenPostIds): array
    {
        $sectionSeenPostIds = [];
        $result = [];

        foreach ($posts as $post) {
            $postId = $this->extractPostId($post);
            if ($postId > 0) {
                if (isset($globalSeenPostIds[$postId]) || isset($sectionSeenPostIds[$postId])) {
                    continue;
                }
                $sectionSeenPostIds[$postId] = true;
                $globalSeenPostIds[$postId] = true;
            }
            $result[] = $post;
        }

        return $result;
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
