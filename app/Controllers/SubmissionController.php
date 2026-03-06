<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\RateLimiter;
use App\Core\Validator;
use App\Models\CategoryModel;
use App\Models\SubmissionModel;
use App\Models\SubmissionPhotoModel;
use App\Services\AuditService;
use App\Services\UploadService;

final class SubmissionController extends BaseController
{
    public function form(): void
    {
        $this->render('public/submit', [
            'title' => 'Enviar Babado - BabadoVip',
            'categories' => (new CategoryModel())->allActive(),
        ]);
    }

    public function store(): void
    {
        Csrf::ensure();
        $honeypot = trim((string) ($_POST['website'] ?? ''));
        if ($honeypot !== '') {
            http_response_code(400);
            exit('Requisição inválida.');
        }

        $ipHash = hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . config('app.key'));
        $submissionModel = new SubmissionModel();
        $photoModel = new SubmissionPhotoModel();

        if (RateLimiter::tooManySubmissions(\App\Core\Database::getInstance(config('db')), $ipHash, (int) config('security.submission_max_per_hour'))) {
            Flash::set('danger', 'Limite de envios por hora atingido. Tente novamente mais tarde.');
            redirect('/enviar');
        }

        $type = (string) ($_POST['tipo_envio'] ?? '');
        $title = trim((string) ($_POST['titulo'] ?? ''));
        $subtitle = trim((string) ($_POST['subtitulo'] ?? ''));
        $content = trim((string) ($_POST['conteudo'] ?? ''));
        $catId = (int) ($_POST['categoria_sugerida_id'] ?? 0);
        $contact = trim((string) ($_POST['contato'] ?? ''));
        $errors = [];

        if (!Validator::in($type, ['sugestao', 'materia'])) {
            $errors[] = 'Tipo de envio inválido.';
        }
        if (!Validator::required($title) || !Validator::max($title, 180)) {
            $errors[] = 'Título é obrigatório e deve ter até 180 caracteres.';
        }
        if (!Validator::max($subtitle, 255)) {
            $errors[] = 'Subtítulo deve ter até 255 caracteres.';
        }
        if ($content === '') {
            $errors[] = 'Conteúdo é obrigatório.';
        }
        if (!Validator::max($contact, 120)) {
            $errors[] = 'Contato inválido.';
        }

        $photosCount = isset($_FILES['fotos']['name']) && is_array($_FILES['fotos']['name']) ? count(array_filter($_FILES['fotos']['name'])) : 0;
        if ($photosCount > 20) {
            $errors[] = 'Máximo de 20 fotos por envio.';
        }
        if ($errors) {
            flash_old($_POST);
            Flash::set('danger', implode(' ', $errors));
            redirect('/enviar');
        }

        $protocol = $submissionModel->generateProtocol();
        $submissionId = $submissionModel->create([
            'protocolo' => $protocol,
            'tipo_envio' => $type,
            'titulo' => $title,
            'subtitulo' => $subtitle ?: null,
            'conteudo' => $this->sanitizeRichText($content),
            'categoria_sugerida_id' => $catId ?: null,
            'nome_leitor' => trim((string) ($_POST['nome_leitor'] ?? '')) ?: null,
            'contato' => $contact ?: null,
            'anonimo' => isset($_POST['anonimo']) ? 1 : 0,
            'status' => 'pendente',
            'ip_hash' => $ipHash,
            'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);

        $savedPhotos = [];
        if ($photosCount > 0) {
            $dir = PUBLIC_PATH . '/uploads/submissions/' . date('Y/m') . '/sub-' . $submissionId . '/';
            $uploadService = new UploadService();
            try {
                $saved = $uploadService->processMultiple($_FILES['fotos'], $dir, 20);
            } catch (\RuntimeException $exception) {
                $submissionModel->delete($submissionId);
                Flash::set('danger', $this->friendlyUploadMessage($exception->getMessage()));
                redirect('/enviar');
            }
            foreach ($saved as $i => $item) {
                $relative = str_replace('\\', '/', str_replace(PUBLIC_PATH . '/', '', $item['full_path']));
                $thumbPath = dirname($item['full_path']) . '/thumb-' . basename($item['full_path']);
                $uploadService->createThumbnail($item['full_path'], $thumbPath);
                $savedPhotos[] = [
                    'arquivo' => $relative,
                    'legenda' => null,
                    'ordem' => $i,
                ];
            }
            $photoModel->createMany($submissionId, $savedPhotos);
        }

        AuditService::log('submission_created', ['submission_id' => $submissionId, 'protocol' => $protocol]);
        clear_old();
        Flash::set('success', 'Recebemos! Está em moderação. Protocolo: ' . $protocol);
        redirect('/enviar');
    }

    private function friendlyUploadMessage(string $rawMessage): string
    {
        if (str_contains($rawMessage, 'Arquivo excede o limite permitido')) {
            return 'Cada foto pode ter no maximo 5 MB.';
        }
        if (str_contains($rawMessage, 'Tipo de arquivo')) {
            return 'Tipo de arquivo nao permitido. Use JPG, PNG, WEBP ou AVIF.';
        }
        return 'Nao foi possivel processar as fotos. Verifique os arquivos e tente novamente.';
    }
}
