<?php declare(strict_types=1); use App\Core\Auth; use App\Core\Csrf; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin - BabadoVip') ?></title>
    <?php $cssVer = @filemtime(PUBLIC_PATH . '/assets/css/app.css') ?: time(); ?>
    <?php $jsVer = @filemtime(PUBLIC_PATH . '/assets/js/app.js') ?: time(); ?>
    <link rel="icon" type="image/png" href="<?= e(url('img/favicon2.png')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css?v=' . $cssVer)) ?>">
</head>
<body class="admin-body <?= Auth::check() ? 'admin-auth' : 'admin-guest' ?>">
<?php if (Auth::check()): ?>
<aside class="admin-sidebar">
    <a class="brand" href="<?= e(url('/admin/home-builder')) ?>"><span class="brand-babado">Babado</span><span class="brand-vip">Vip</span></a>
    <a href="<?= e(url('/admin/home-builder')) ?>">Home Builder</a>
    <a href="<?= e(url('/admin/posts')) ?>">Materias</a>
    <a href="<?= e(url('/admin/fofocas')) ?>">Fofocas Rapidas</a>
    <a href="<?= e(url('/admin/submissions')) ?>">Sugestões dos Leitores</a>
    <a href="<?= e(url('/admin/messages')) ?>">Mensagens</a>
    <form action="<?= e(url('/admin/logout')) ?>" method="post">
        <?= Csrf::field() ?>
        <button type="submit" class="btn-danger">Sair</button>
    </form>
</aside>
<?php endif; ?>
<main class="admin-main">
    <?php foreach (($flash ?? []) as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<script src="<?= e(url('assets/js/app.js?v=' . $jsVer)) ?>" defer></script>
</body>
</html>
