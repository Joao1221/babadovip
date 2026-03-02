<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Models\PostCommentModel;
use App\Models\PostModel;
use App\Models\PostPhotoModel;

final class PostController extends BaseController
{
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
        $allowComments = (($post['categoria_slug'] ?? '') === 'fofocas-rapidas');
        $comments = $allowComments ? $commentModel->listApprovedByPost((int) $post['id']) : [];

        $this->render('public/post', [
            'title' => $post['titulo'] . ' - BabadoVip',
            'post' => $post,
            'photos' => $photos,
            'allowComments' => $allowComments,
            'comments' => $comments,
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
        if (($post['categoria_slug'] ?? '') !== 'fofocas-rapidas') {
            Flash::set('danger', 'Comentarios disponiveis apenas para Fofocas Rapidas.');
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
}
