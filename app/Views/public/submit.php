<?php use App\Core\Csrf; ?>
<section class="form-wrap">
    <h1>Enviar Babado</h1>
    <p class="muted">Mande sua sugestão ou matéria. Nossa equipe vai moderar antes de publicar.</p>
    <form method="post" enctype="multipart/form-data" class="grid-form">
        <?= Csrf::field() ?>
        <input type="text" name="website" class="hidden-honeypot" tabindex="-1" autocomplete="off">
        <label>Tipo de envio
            <select name="tipo_envio" required>
                <option value="sugestao">Sugestão</option>
                <option value="materia">Matéria</option>
            </select>
        </label>
        <label>Título
            <input type="text" name="titulo" maxlength="180" required value="<?= e((string) old('titulo')) ?>">
        </label>
        <label>Subtítulo
            <input type="text" name="subtitulo" maxlength="255" value="<?= e((string) old('subtitulo')) ?>">
        </label>
        <label>Categoria sugerida
            <select name="categoria_sugerida_id" required>
                <option value="">Selecione</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>"><?= e($cat['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Seu nome/apelido
            <input type="text" name="nome_leitor" maxlength="120" value="<?= e((string) old('nome_leitor')) ?>">
        </label>
        <label>Contato (WhatsApp ou e-mail)
            <input type="text" name="contato" maxlength="120" value="<?= e((string) old('contato')) ?>">
        </label>
        <label class="full">Texto
            <textarea name="conteudo" rows="8" required><?= e((string) old('conteudo')) ?></textarea>
        </label>
        <label class="full">Fotos (até 20)
            <input type="file" name="fotos[]" accept=".jpg,.jpeg,.png,.webp" multiple data-max-files="20">
        </label>
        <label class="check full">
            <input type="checkbox" name="anonimo" value="1"> Manter anonimato
        </label>
        <button class="btn-primary full" type="submit">Enviar para moderação</button>
    </form>
</section>
