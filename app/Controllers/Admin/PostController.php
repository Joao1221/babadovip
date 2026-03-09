<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\CategoryModel;
use App\Models\HomeCardModel;
use App\Models\PostModel;
use App\Models\PostPhotoModel;
use App\Models\SiteVisitModel;
use App\Services\UploadService;

final class PostController extends BaseController
{
    public function dashboard(): void
    {
        Auth::requireAdmin();
        $postModel = new PostModel();
        $latest = $postModel->listAdmin([], 12, 0);
        $this->render('admin/dashboard', [
            'title' => 'Dashboard - BabadoVip',
            'latest' => $latest,
        ], 'admin');
    }

    public function index(): void
    {
        Auth::requireAdmin();
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'categoria_id' => (int) ($_GET['categoria_id'] ?? 0),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'data' => trim((string) ($_GET['data'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $postModel = new PostModel();
        $siteVisitModel = new SiteVisitModel();
        $posts = $postModel->listAdmin($filters, $limit, $offset);
        $count = $postModel->countAdmin($filters);
        $totalViews = $postModel->totalViewsAdmin($filters);
        $topViewed = $postModel->topViewedAdmin(5);
        $siteVisitsTotal = $siteVisitModel->totalVisits();
        $siteVisitsToday = $siteVisitModel->todayVisits();
        $pages = (int) max(1, ceil($count / $limit));

        $this->render('admin/posts/index', [
            'title' => 'Materias - Admin',
            'posts' => $posts,
            'totalViews' => $totalViews,
            'topViewed' => $topViewed,
            'siteVisitsTotal' => $siteVisitsTotal,
            'siteVisitsToday' => $siteVisitsToday,
            'filters' => $filters,
            'categories' => (new CategoryModel())->allActive(),
            'page' => $page,
            'pages' => $pages,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $this->render('admin/posts/form', [
            'title' => 'Nova Materia',
            'post' => null,
            'photos' => [],
            'categories' => (new CategoryModel())->allActive(),
            'placements' => [],
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
        $post = (new PostModel())->findById((int) $id);
        if (!$post) {
            http_response_code(404);
            exit('Post nao encontrado.');
        }
        $this->render('admin/posts/form', [
            'title' => 'Editar Materia',
            'post' => $post,
            'photos' => (new PostPhotoModel())->listByPost((int) $id),
            'categories' => (new CategoryModel())->allActive(),
            'placements' => (new HomeCardModel())->listPlacementsByPost((int) $id),
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $this->save((int) $id);
    }

    private function save(?int $id): void
    {
        $postModel = new PostModel();
        $photoModel = new PostPhotoModel();
        $uploadService = new UploadService();
        $existingPost = $id ? $postModel->findById($id) : null;
        if ($id && !$existingPost) {
            Flash::set('danger', 'Materia nao encontrada.');
            redirect('/admin/posts');
        }

        $title = trim((string) ($_POST['titulo'] ?? ''));
        $titleForSlug = trim((string) preg_replace('/<br\s*\/?>/i', ' ', $title));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        if ($slug === '') {
            $slug = $postModel->generateUniqueSlug($titleForSlug !== '' ? $titleForSlug : $title, $id);
        } else {
            $slug = $postModel->generateUniqueSlug($slug, $id);
        }
        $status = in_array(($_POST['status'] ?? ''), ['draft', 'published', 'scheduled'], true) ? $_POST['status'] : 'draft';
        $publishedAt = trim((string) ($_POST['publicado_em'] ?? '')) ?: null;
        $content = $this->sanitizeRichText((string) ($_POST['conteudo_html'] ?? ''));
        $homeSubheadlines = $this->sanitizeHomeSubheadlines((string) ($_POST['subchamadas_home'] ?? ''));
        $overlayTitleColor = strtoupper(trim((string) ($_POST['overlay_titulo_cor'] ?? '#FFFFFF')));
        if (!preg_match('/^#[0-9A-F]{6}$/', $overlayTitleColor)) {
            $overlayTitleColor = '#FFFFFF';
        }

        if ($title === '' || $content === '') {
            Flash::set('danger', 'Titulo e conteudo sao obrigatorios.');
            redirect($id ? '/admin/posts/' . $id . '/editar' : '/admin/posts/novo');
        }

        $removeCover = isset($_POST['remover_imagem_capa']) && (string) $_POST['remover_imagem_capa'] === '1';
        $coverPath = $existingPost['imagem_capa'] ?? null;
        if ($removeCover) {
            $coverPath = null;
        }
        $removeMobileCover = isset($_POST['remover_imagem_capa_mobile']) && (string) $_POST['remover_imagem_capa_mobile'] === '1';
        $mobileCoverPath = $existingPost['imagem_capa_mobile'] ?? null;
        if ($removeMobileCover) {
            $mobileCoverPath = null;
        }
        if (isset($_FILES['imagem_capa']) && ($_FILES['imagem_capa']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $tmpFolder = PUBLIC_PATH . '/uploads/tmp/capa-desktop-' . date('YmdHis') . '-' . bin2hex(random_bytes(3));
            $saved = $uploadService->processMultiple($_FILES['imagem_capa'], $tmpFolder, 1);
            if ($saved) {
                $coverPath = str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $saved[0]['full_path']));
            }
        }
        if (isset($_FILES['imagem_capa_mobile']) && ($_FILES['imagem_capa_mobile']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $tmpFolder = PUBLIC_PATH . '/uploads/tmp/capa-mobile-' . date('YmdHis') . '-' . bin2hex(random_bytes(3));
            $saved = $uploadService->processMultiple($_FILES['imagem_capa_mobile'], $tmpFolder, 1);
            if ($saved) {
                $mobileCoverPath = str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $saved[0]['full_path']));
            }
        }

        $data = [
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
            'autor_admin_id' => (int) (Auth::admin()['id'] ?? 1),
            'titulo' => $title,
            'subtitulo' => trim((string) ($_POST['subtitulo'] ?? '')) ?: null,
            'slug' => $slug,
            'conteudo_html' => $content,
            'status' => $status,
            'publicado_em' => $publishedAt ?: ($status === 'published' ? now() : null),
            'is_breaking' => isset($_POST['is_breaking']) ? 1 : 0,
            'is_exclusivo' => isset($_POST['is_exclusivo']) ? 1 : 0,
            'is_vip' => isset($_POST['is_vip']) ? 1 : 0,
            'verificacao' => in_array($_POST['verificacao'] ?? 'rumor', ['rumor', 'confirmado'], true) ? $_POST['verificacao'] : 'rumor',
            'imagem_capa' => $coverPath,
            'imagem_capa_mobile' => $mobileCoverPath,
            'tags' => trim((string) ($_POST['tags'] ?? '')) ?: null,
            'tempo_leitura' => (int) ($_POST['tempo_leitura'] ?? 3),
            'event_data' => trim((string) ($_POST['event_data'] ?? '')) ?: null,
            'event_hora' => trim((string) ($_POST['event_hora'] ?? '')) ?: null,
            'event_local' => trim((string) ($_POST['event_local'] ?? '')) ?: null,
            'event_bairro_cidade' => trim((string) ($_POST['event_bairro_cidade'] ?? '')) ?: null,
            'subchamadas_home' => $homeSubheadlines,
            'overlay_titulo_cor' => $overlayTitleColor,
        ];
        if ($id) {
            $postModel->update($id, $data);
            $postId = $id;
        } else {
            $postId = $postModel->create($data);
        }

        try {
            $gallery = $this->collectGallery($postId, $uploadService);
        } catch (\RuntimeException $exception) {
            Flash::set('danger', $exception->getMessage());
            redirect($id ? '/admin/posts/' . $id . '/editar' : '/admin/posts/novo');
        }
        if (count($gallery) > 20) {
            Flash::set('danger', 'Maximo de 20 fotos na galeria.');
            redirect($id ? '/admin/posts/' . $id . '/editar' : '/admin/posts/novo');
        }
        $photoModel->replaceForPost($postId, $gallery);

        $finalCoverPath = $this->finalizeCoverImage($coverPath, $postId, 'capa');
        if ($finalCoverPath !== '' && $finalCoverPath !== null) {
            $finalCoverAbsolute = PUBLIC_PATH . '/' . ltrim($finalCoverPath, '/');
            $social = dirname($finalCoverAbsolute) . '/social-' . pathinfo($finalCoverAbsolute, PATHINFO_FILENAME) . '.jpg';
            $uploadService->createThumbnail($finalCoverAbsolute, $social, 1200);
        }
        $finalMobileCoverPath = $this->finalizeCoverImage($mobileCoverPath, $postId, 'capa-mobile');
        if ($finalCoverPath !== $coverPath || $finalMobileCoverPath !== $mobileCoverPath) {
            $postModel->update($postId, array_merge($data, [
                'imagem_capa' => $finalCoverPath,
                'imagem_capa_mobile' => $finalMobileCoverPath,
            ]));
        }

        Flash::set('success', 'Materia salva com sucesso.');
        redirect('/admin/posts/' . $postId . '/editar');
    }

    private function finalizeCoverImage(?string $coverPath, int $postId, string $folderName): ?string
    {
        $coverPath = trim((string) $coverPath);
        if ($coverPath === '') {
            return null;
        }
        if (!str_contains($coverPath, 'uploads/tmp/')) {
            return $coverPath;
        }

        $source = PUBLIC_PATH . '/' . ltrim($coverPath, '/');
        if (!is_file($source)) {
            return null;
        }

        $finalDir = PUBLIC_PATH . '/uploads/posts/' . date('Y/m') . '/post-' . $postId . '/' . $folderName;
        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0775, true);
        }
        $final = $finalDir . '/' . basename($source);
        @rename($source, $final);
        if (!is_file($final) && is_file($source)) {
            @copy($source, $final);
        }
        if (!is_file($final)) {
            return null;
        }

        return str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $final));
    }

    private function collectGallery(int $postId, UploadService $uploadService): array
    {
        $result = [];
        $existingFiles = $_POST['existing_fotos'] ?? [];
        $existingLegends = $_POST['existing_legendas'] ?? [];
        $existingOrder = $_POST['existing_ordens'] ?? [];
        $newLegends = $_POST['new_legendas'] ?? [];

        foreach ($existingFiles as $i => $file) {
            $result[] = [
                'arquivo' => (string) $file,
                'legenda' => $existingLegends[$i] ?? null,
                'ordem' => (int) ($existingOrder[$i] ?? $i),
            ];
        }

        $uploadCount = isset($_FILES['fotos']['name']) && is_array($_FILES['fotos']['name']) ? count(array_filter($_FILES['fotos']['name'])) : 0;
        if ($uploadCount > 0) {
            if (count($result) + $uploadCount > 20) {
                throw new \RuntimeException('A galeria excede 20 fotos.');
            }
            $dir = PUBLIC_PATH . '/uploads/posts/' . date('Y/m') . '/post-' . $postId . '/galeria';
            $saved = $uploadService->processMultiple($_FILES['fotos'], $dir, 20);
            foreach ($saved as $i => $item) {
                $relative = str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $item['full_path']));
                $thumb = dirname($item['full_path']) . '/thumb-' . basename($item['full_path']);
                $uploadService->createThumbnail($item['full_path'], $thumb);
                $legend = trim((string) ($newLegends[$i] ?? ''));
                $result[] = [
                    'arquivo' => $relative,
                    'legenda' => $legend !== '' ? mb_substr($legend, 0, 255) : null,
                    'ordem' => count($result) + $i,
                ];
            }
        }

        usort($result, static fn(array $a, array $b): int => (int) $a['ordem'] <=> (int) $b['ordem']);
        return array_values($result);
    }

    private function sanitizeHomeSubheadlines(string $raw): ?string
    {
        $lines = preg_split('/\R/u', $raw) ?: [];
        $result = [];
        foreach ($lines as $line) {
            $clean = trim((string) preg_replace('/\s+/u', ' ', (string) $line));
            if ($clean === '') {
                continue;
            }
            $result[] = mb_substr($clean, 0, 255);
            if (count($result) >= 5) {
                break;
            }
        }
        if ($result === []) {
            return null;
        }
        return implode("\n", $result);
    }

    public function duplicate(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        $postModel = new PostModel();
        $photoModel = new PostPhotoModel();
        $post = $postModel->findById((int) $id);
        if (!$post) {
            Flash::set('danger', 'Materia nao encontrada.');
            redirect('/admin/posts');
        }
        $newTitle = $post['titulo'] . ' (Copia)';
        $newId = $postModel->create([
            'categoria_id' => (int) $post['categoria_id'],
            'autor_admin_id' => (int) (Auth::admin()['id'] ?? 1),
            'titulo' => $newTitle,
            'subtitulo' => $post['subtitulo'],
            'slug' => $postModel->generateUniqueSlug($newTitle),
            'conteudo_html' => $post['conteudo_html'],
            'status' => 'draft',
            'publicado_em' => null,
            'is_breaking' => (int) $post['is_breaking'],
            'is_exclusivo' => (int) $post['is_exclusivo'],
            'is_vip' => (int) $post['is_vip'],
            'verificacao' => $post['verificacao'],
            'imagem_capa' => $post['imagem_capa'],
            'imagem_capa_mobile' => $post['imagem_capa_mobile'] ?? null,
            'tags' => $post['tags'],
            'tempo_leitura' => (int) $post['tempo_leitura'],
            'event_data' => $post['event_data'],
            'event_hora' => $post['event_hora'],
            'event_local' => $post['event_local'],
            'event_bairro_cidade' => $post['event_bairro_cidade'],
            'subchamadas_home' => $post['subchamadas_home'] ?? null,
            'overlay_titulo_cor' => $post['overlay_titulo_cor'] ?? '#FFFFFF',
        ]);
        $photoModel->replaceForPost($newId, $photoModel->listByPost((int) $id));
        Flash::set('success', 'Materia duplicada.');
        redirect('/admin/posts/' . $newId . '/editar');
    }

    public function delete(string $id): void
    {
        Auth::requireAdmin();
        Csrf::ensure();
        (new PostModel())->delete((int) $id);
        Flash::set('success', 'Materia excluida.');
        redirect('/admin/posts');
    }
}
