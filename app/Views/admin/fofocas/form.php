<?php use App\Core\Csrf; $isEdit = is_array($item); ?>
<section class="section-head">
    <h1><?= $isEdit ? 'Editar Fofoca Rapida #' . (int) $item['id'] : 'Nova Fofoca Rapida' ?></h1>
    <a class="btn-small" href="<?= e(url('/admin/fofocas')) ?>">Voltar</a>
</section>

<form method="post" class="grid-form" action="<?= e($isEdit ? url('/admin/fofocas/' . $item['id'] . '/editar') : url('/admin/fofocas')) ?>">
    <?= Csrf::field() ?>
    <label class="full">Titulo
        <input type="text" name="titulo" maxlength="220" required value="<?= e((string) ($item['titulo'] ?? '')) ?>">
    </label>
    <label class="full">Texto curto
        <textarea name="subtitulo" rows="4" maxlength="500"><?= e((string) ($item['subtitulo'] ?? '')) ?></textarea>
    </label>
    <label>Data/Hora de publicacao
        <input type="datetime-local" name="publicado_em" value="<?= !empty($item['publicado_em']) ? e(date('Y-m-d\TH:i', strtotime((string) $item['publicado_em']))) : e(date('Y-m-d\TH:i')) ?>">
    </label>
    <label class="check"><input type="checkbox" name="ativo" value="1" <?= !array_key_exists('ativo', (array) $item) || !empty($item['ativo']) ? 'checked' : '' ?>> Exibir em Fofocas Quentes</label>
    <button type="submit" class="btn-primary full">Salvar Fofoca Rapida</button>
</form>
