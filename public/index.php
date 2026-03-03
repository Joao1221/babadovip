<?php
declare(strict_types=1);

$config = require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\HomeBuilderController;
use App\Controllers\Admin\PostController as AdminPostController;
use App\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Controllers\Admin\FofocaRapidaController as AdminFofocaRapidaController;
use App\Controllers\ContactController;
use App\Controllers\PostController;
use App\Controllers\PublicController;
use App\Controllers\SubmissionController;
use App\Core\Router;

$router = new Router();

$router->get('/', [PublicController::class, 'home']);
$router->get('/ultimas', [PublicController::class, 'ultimas']);
$router->get('/fofocas', [PublicController::class, 'fofocas']);
$router->get('/categoria/{slug}', [PublicController::class, 'category']);
$router->get('/materia/{slug}', [PostController::class, 'show']);
$router->post('/materia/{slug}/comentarios', [PostController::class, 'storeComment']);
$router->get('/enviar', [SubmissionController::class, 'form']);
$router->post('/enviar', [SubmissionController::class, 'store']);
$router->get('/contato', [ContactController::class, 'form']);
$router->post('/contato', [ContactController::class, 'store']);

$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->post('/admin/logout', [AuthController::class, 'logout']);

$router->get('/admin', [HomeBuilderController::class, 'index']);
$router->get('/admin/posts', [AdminPostController::class, 'index']);
$router->get('/admin/posts/novo', [AdminPostController::class, 'create']);
$router->post('/admin/posts', [AdminPostController::class, 'store']);
$router->get('/admin/posts/{id}/editar', [AdminPostController::class, 'edit']);
$router->post('/admin/posts/{id}/editar', [AdminPostController::class, 'update']);
$router->post('/admin/posts/{id}/duplicar', [AdminPostController::class, 'duplicate']);
$router->post('/admin/posts/{id}/excluir', [AdminPostController::class, 'delete']);

$router->get('/admin/home-builder', [HomeBuilderController::class, 'index']);
$router->post('/admin/home-builder/secao/{id}', [HomeBuilderController::class, 'updateSection']);
$router->post('/admin/home-builder/secao/{sectionId}/card/{position}', [HomeBuilderController::class, 'upsertCard']);
$router->post('/admin/home-builder/secao/{sectionId}/card/{position}/remover', [HomeBuilderController::class, 'removeCard']);

$router->get('/admin/fofocas', [AdminFofocaRapidaController::class, 'index']);
$router->get('/admin/fofocas/nova', [AdminFofocaRapidaController::class, 'create']);
$router->post('/admin/fofocas', [AdminFofocaRapidaController::class, 'store']);
$router->get('/admin/fofocas/{id}/editar', [AdminFofocaRapidaController::class, 'edit']);
$router->post('/admin/fofocas/{id}/editar', [AdminFofocaRapidaController::class, 'update']);
$router->post('/admin/fofocas/{id}/excluir', [AdminFofocaRapidaController::class, 'delete']);

$router->get('/admin/submissions', [AdminSubmissionController::class, 'index']);
$router->get('/admin/submissions/{id}', [AdminSubmissionController::class, 'show']);
$router->post('/admin/submissions/{id}/aprovar', [AdminSubmissionController::class, 'approve']);
$router->post('/admin/submissions/{id}/rejeitar', [AdminSubmissionController::class, 'reject']);
$router->post('/admin/submissions/{id}/excluir', [AdminSubmissionController::class, 'delete']);
$router->get('/admin/messages', [AdminContactMessageController::class, 'index']);
$router->get('/admin/messages/{id}', [AdminContactMessageController::class, 'show']);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = parse_url($config['app']['url'], PHP_URL_PATH) ?: '';
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $uri);
