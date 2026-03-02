<?php use App\Core\Csrf; ?>
<section class="form-wrap">
    <h1>Contato</h1>
    <p class="muted">Envie sua mensagem para a equipe BabadoVip.</p>
    <form method="post" class="grid-form">
        <?= Csrf::field() ?>
        <input type="text" name="website" class="hidden-honeypot" tabindex="-1" autocomplete="off">
        <label>Nome
            <input type="text" name="nome" maxlength="120" required value="<?= e((string) old('nome')) ?>">
        </label>
        <label>Contato (e-mail ou WhatsApp)
            <input type="text" name="contato" maxlength="120" value="<?= e((string) old('contato')) ?>">
        </label>
        <label class="full">Assunto
            <input type="text" name="assunto" maxlength="160" required value="<?= e((string) old('assunto')) ?>">
        </label>
        <label class="full">Mensagem
            <textarea name="mensagem" rows="8" maxlength="3000" required><?= e((string) old('mensagem')) ?></textarea>
        </label>
        <button class="btn-primary full" type="submit">Enviar mensagem</button>
    </form>
</section>
