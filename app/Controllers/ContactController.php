<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\ContactMessageModel;
use App\Services\AuditService;

final class ContactController extends BaseController
{
    public function form(): void
    {
        $this->render('public/contact', [
            'title' => 'Contato - BabadoVip',
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

        $name = trim((string) ($_POST['nome'] ?? ''));
        $contact = trim((string) ($_POST['contato'] ?? ''));
        $subject = trim((string) ($_POST['assunto'] ?? ''));
        $message = trim((string) ($_POST['mensagem'] ?? ''));
        $errors = [];

        if (!Validator::required($name) || !Validator::max($name, 120)) {
            $errors[] = 'Nome é obrigatório e deve ter até 120 caracteres.';
        }
        if (!Validator::required($subject) || !Validator::max($subject, 160)) {
            $errors[] = 'Assunto é obrigatório e deve ter até 160 caracteres.';
        }
        if (!Validator::required($message) || !Validator::max($message, 3000)) {
            $errors[] = 'Mensagem é obrigatória e deve ter até 3000 caracteres.';
        }
        if (!Validator::max($contact, 120)) {
            $errors[] = 'Contato inválido.';
        }

        if ($errors) {
            flash_old($_POST);
            Flash::set('danger', implode(' ', $errors));
            redirect('/contato');
        }

        $ipHash = hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . config('app.key'));
        $model = new ContactMessageModel();
        if ($model->countByIpLastHour($ipHash) >= 10) {
            Flash::set('danger', 'Limite de mensagens por hora atingido. Tente mais tarde.');
            redirect('/contato');
        }

        $id = $model->create([
            'nome' => $name,
            'contato' => $contact ?: null,
            'assunto' => $subject,
            'mensagem' => $message,
            'ip_hash' => $ipHash,
            'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);

        AuditService::log('contact_message_created', ['message_id' => $id]);
        clear_old();
        Flash::set('success', 'Mensagem enviada com sucesso. Em breve retornaremos.');
        redirect('/contato');
    }
}
