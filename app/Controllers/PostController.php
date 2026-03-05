<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Models\PostCommentModel;
use App\Models\PostModel;
use App\Models\PostPhotoModel;
use App\Services\UploadService;

final class PostController extends BaseController
{
    private const COMMENTABLE_CATEGORY_SLUGS = ['fofocas-rapidas', 'eventos-agenda'];
    private const SITE_URL = 'https://www.babadovip.com.br';
    private const DEFAULT_SOCIAL_IMAGE = 'https://www.babadovip.com.br/public/img/babado-vip.png';

    public function show(string $slug): void
    {
        $postModel = new PostModel();
        $photoModel = new PostPhotoModel();
        $commentModel = new PostCommentModel();

        $post = $postModel->findBySlugPublic($slug);
        if (!$post) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Materia nao encontrada']);
            return;
        }

        $postModel->incrementViews((int) $post['id']);
        $photos = $photoModel->listByPost((int) $post['id']);
        $allowComments = $this->allowsComments($post);
        $comments = $allowComments ? $commentModel->listApprovedByPost((int) $post['id']) : [];
        $metaDescription = $this->buildMetaDescription($post);
        $metaImage = $this->buildMetaImage($post, $photos);
        $canonicalUrl = $this->siteUrl('/materia/' . (string) $post['slug']);

        $metaTitle = $this->buildMetaTitle($post);

        $this->render('public/post', [
            'title' => $post['titulo'] . ' - BabadoVip',
            'post' => $post,
            'photos' => $photos,
            'allowComments' => $allowComments,
            'comments' => $comments,
            'canonicalUrl' => $canonicalUrl,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage' => $metaImage,
            'metaType' => 'article',
        ]);
    }

    public function storeComment(string $slug): void
    {
        Csrf::ensure();

        $post = (new PostModel())->findBySlugPublic($slug);
        if (!$post) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Materia nao encontrada']);
            return;
        }
        if (!$this->allowsComments($post)) {
            Flash::set('danger', 'Comentarios disponiveis apenas para Fofocas e Eventos/Agenda.');
            redirect('/materia/' . $slug);
        }

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $mensagem = trim((string) ($_POST['mensagem'] ?? ''));
        if ($nome === '' || $mensagem === '') {
            Flash::set('danger', 'Preencha nome e comentario.');
            redirect('/materia/' . $slug . '#comentarios');
        }
        if (mb_strlen($nome) > 100 || mb_strlen($mensagem) > 1200) {
            Flash::set('danger', 'Comentario excede o limite permitido.');
            redirect('/materia/' . $slug . '#comentarios');
        }

        $authorMeta = collect_author_metadata();
        (new PostCommentModel())->create(
            (int) $post['id'],
            $nome,
            $mensagem,
            $authorMeta['ip_address'],
            $authorMeta['user_agent'],
            $authorMeta['request_metadata']
        );
        Flash::set('success', 'Comentario publicado.');
        redirect('/materia/' . $slug . '#comentarios');
    }

    private function allowsComments(array $post): bool
    {
        $slug = (string) ($post['categoria_slug'] ?? '');
        return in_array($slug, self::COMMENTABLE_CATEGORY_SLUGS, true);
    }

    private function buildMetaTitle(array $post): string
    {
        $title = $this->cleanText((string) ($post['titulo'] ?? ''));
        if ($title === '') {
            return 'BabadoVip';
        }

        return $this->truncateText($title, 90);
    }

    private function buildMetaDescription(array $post): string
    {
        $subtitle = $this->cleanText((string) ($post['subtitulo'] ?? ''));
        if ($subtitle !== '') {
            return $this->truncateText($subtitle, 200);
        }

        $plainContent = $this->cleanText((string) ($post['conteudo_html'] ?? ''));
        if ($plainContent === '') {
            return 'Leia a materia completa no BabadoVip.';
        }

        return $this->truncateText($plainContent, 200);
    }

    private function buildMetaImage(array $post, array $photos): string
    {
        $imagePath = trim((string) ($post['imagem_capa'] ?? ''));
        if ($imagePath === '' && !empty($photos[0]['arquivo'])) {
            $imagePath = trim((string) $photos[0]['arquivo']);
        }

        $imagePath = str_replace('\\', '/', $imagePath);
        if ($imagePath !== '' && preg_match('#^https?://#i', $imagePath) === 1) {
            return $imagePath;
        }

        $imagePath = ltrim($imagePath, '/');
        if (str_starts_with($imagePath, 'public/')) {
            $imagePath = substr($imagePath, strlen('public/'));
        }

        if ($imagePath === '') {
            return self::DEFAULT_SOCIAL_IMAGE;
        }

        $socialPath = $this->resolveSocialMetaImage($imagePath);
        if ($socialPath !== null) {
            $imagePath = $socialPath;
        }

        return $this->siteUrl($imagePath);
    }

    private function resolveSocialMetaImage(string $imagePath): ?string
    {
        $normalized = ltrim(str_replace('\\', '/', $imagePath), '/');
        if (!str_starts_with($normalized, 'uploads/')) {
            return null;
        }

        $sourceFullPath = PUBLIC_PATH . '/' . $normalized;
        if (!is_file($sourceFullPath)) {
            return null;
        }

        $socialFileName = 'social-' . pathinfo($sourceFullPath, PATHINFO_FILENAME) . '.jpg';
        $socialFullPath = dirname($sourceFullPath) . '/' . $socialFileName;
        $mustRegenerateSocial = true;
        if (is_file($socialFullPath)) {
            $socialImageInfo = @getimagesize($socialFullPath);
            $socialWidth = (int) ($socialImageInfo[0] ?? 0);
            $socialHeight = (int) ($socialImageInfo[1] ?? 0);
            $mustRegenerateSocial = $socialWidth !== 1200 || $socialHeight !== 630;
        }

        if ($mustRegenerateSocial) {
            try {
                (new UploadService())->createSocialCard($sourceFullPath, $socialFullPath, 1200, 630);
            } catch (\Throwable) {
                return null;
            }
        }

        if (!is_file($socialFullPath)) {
            return null;
        }

        $socialRelativePath = str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $socialFullPath));
        return $socialRelativePath !== '' ? $socialRelativePath : null;
    }

    private function cleanText(string $value): string
    {
        $value = str_ireplace(['<br />', '<br/>', '<br>'], ' ', $value);
        return trim((string) preg_replace('/\s+/u', ' ', strip_tags($value)));
    }

    private function truncateText(string $value, int $maxChars): string
    {
        if (mb_strlen($value) <= $maxChars) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $maxChars - 3)) . '...';
    }

    private function siteUrl(string $path = ''): string
    {
        return rtrim(self::SITE_URL, '/') . '/' . ltrim($path, '/');
    }
}
