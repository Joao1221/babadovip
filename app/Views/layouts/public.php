<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        $metaTitle = trim((string) ($metaTitle ?? $title ?? 'BabadoVip'));
        $metaDescription = trim((string) ($metaDescription ?? ''));
        $metaImage = trim((string) ($metaImage ?? ''));
        $metaType = trim((string) ($metaType ?? 'website'));
        $canonicalUrl = trim((string) ($canonicalUrl ?? ''));
        $metaImageMime = '';
        $metaImageWidth = 0;
        $metaImageHeight = 0;
        if ($metaImage !== '') {
            $metaImagePath = parse_url($metaImage, PHP_URL_PATH);
            if (is_string($metaImagePath) && $metaImagePath !== '') {
                $appBasePath = (string) (parse_url((string) config('app.url'), PHP_URL_PATH) ?? '');
                if ($appBasePath !== '' && str_starts_with($metaImagePath, $appBasePath . '/')) {
                    $metaImagePath = substr($metaImagePath, strlen($appBasePath));
                } elseif ($appBasePath !== '' && $metaImagePath === $appBasePath) {
                    $metaImagePath = '/';
                }
                $metaImageLocalPath = PUBLIC_PATH . '/' . ltrim($metaImagePath, '/');
                if (is_file($metaImageLocalPath)) {
                    $metaImageInfo = @getimagesize($metaImageLocalPath);
                    if (is_array($metaImageInfo)) {
                        $metaImageWidth = (int) ($metaImageInfo[0] ?? 0);
                        $metaImageHeight = (int) ($metaImageInfo[1] ?? 0);
                        $metaImageMime = (string) ($metaImageInfo['mime'] ?? '');
                    }
                }
            }
            if ($metaImageMime === '') {
                $path = strtolower((string) (parse_url($metaImage, PHP_URL_PATH) ?? ''));
                if (str_ends_with($path, '.png')) {
                    $metaImageMime = 'image/png';
                } elseif (str_ends_with($path, '.webp')) {
                    $metaImageMime = 'image/webp';
                } elseif (str_ends_with($path, '.jpg') || str_ends_with($path, '.jpeg')) {
                    $metaImageMime = 'image/jpeg';
                }
            }
        }
    ?>
    <title><?= e($title ?? 'BabadoVip') ?></title>
    <meta name="description" content="<?= e($metaDescription !== '' ? $metaDescription : 'Noticias, eventos e fofocas de Capela e regiao no BabadoVip.') ?>">
    <?php if ($canonicalUrl !== ''): ?><link rel="canonical" href="<?= e($canonicalUrl) ?>"><?php endif; ?>
    <meta property="og:site_name" content="BabadoVip">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="<?= e($metaType !== '' ? $metaType : 'website') ?>">
    <meta property="og:title" content="<?= e($metaTitle !== '' ? $metaTitle : 'BabadoVip') ?>">
    <meta property="og:description" content="<?= e($metaDescription !== '' ? $metaDescription : 'Noticias, eventos e fofocas de Capela e regiao no BabadoVip.') ?>">
    <?php if ($canonicalUrl !== ''): ?><meta property="og:url" content="<?= e($canonicalUrl) ?>"><?php endif; ?>
    <?php if ($metaImage !== ''): ?>
        <meta property="og:image" content="<?= e($metaImage) ?>">
        <meta property="og:image:secure_url" content="<?= e($metaImage) ?>">
        <?php if ($metaImageMime !== ''): ?><meta property="og:image:type" content="<?= e($metaImageMime) ?>"><?php endif; ?>
        <?php if ($metaImageWidth > 0): ?><meta property="og:image:width" content="<?= $metaImageWidth ?>"><?php endif; ?>
        <?php if ($metaImageHeight > 0): ?><meta property="og:image:height" content="<?= $metaImageHeight ?>"><?php endif; ?>
    <?php endif; ?>
    <meta name="twitter:card" content="<?= $metaImage !== '' ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= e($metaTitle !== '' ? $metaTitle : 'BabadoVip') ?>">
    <meta name="twitter:description" content="<?= e($metaDescription !== '' ? $metaDescription : 'Noticias, eventos e fofocas de Capela e regiao no BabadoVip.') ?>">
    <?php if ($metaImage !== ''): ?><meta name="twitter:image" content="<?= e($metaImage) ?>"><?php endif; ?>
    <?php $ga4MeasurementId = trim((string) config('analytics.ga4_measurement_id', '')); ?>
    <?php $cssVer = @filemtime(PUBLIC_PATH . '/assets/css/app.css') ?: time(); ?>
    <?php $jsVer = @filemtime(PUBLIC_PATH . '/assets/js/app.js') ?: time(); ?>
    <link rel="icon" type="image/png" href="<?= e(url('img/favicon2.png')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css?v=' . $cssVer)) ?>">
    <?php if ($ga4MeasurementId !== ''): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4MeasurementId) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', <?= json_encode($ga4MeasurementId, JSON_UNESCAPED_SLASHES) ?>);
        </script>
    <?php endif; ?>
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="<?= e(url('/')) ?>" class="brand brand-site">
            <span class="brand-main">
                <span class="brand-babado">Babado</span><span class="brand-vip">Vip</span>
            </span>
            <span class="brand-tagline">Onde os vips aparecem!</span>
        </a>
        <button type="button" class="menu-toggle" aria-label="Abrir menu" aria-controls="siteMenu" aria-expanded="false">☰</button>
        <?php
            $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $basePath = parse_url((string) config('app.url'), PHP_URL_PATH) ?: '';
            if ($basePath !== '' && str_starts_with($currentUri, $basePath)) {
                $currentUri = substr($currentUri, strlen($basePath)) ?: '/';
            }
            $isHome = $currentUri === '/';
            $isSociedade = str_starts_with($currentUri, '/categoria/sociedade-festas');
            $isEventos = str_starts_with($currentUri, '/categoria/eventos-agenda');
            $isFofocas = str_starts_with($currentUri, '/fofocas');
            $isUltimas = str_starts_with($currentUri, '/ultimas');
        ?>
        <nav class="menu" id="siteMenu">
            <a class="<?= $isHome ? 'active' : '' ?>" href="<?= e(url('/')) ?>">Home</a>
            <?php
                $catsBySlug = [];
                foreach (($menuCategories ?? []) as $cat) {
                    $catsBySlug[$cat['slug']] = $cat;
                }
            ?>
            <?php if (!empty($catsBySlug['sociedade-festas'])): ?>
                <a class="<?= $isSociedade ? 'active' : '' ?>" href="<?= e(url('/categoria/' . $catsBySlug['sociedade-festas']['slug'])) ?>">Sociedade & Festas</a>
            <?php endif; ?>
            <?php if (!empty($catsBySlug['eventos-agenda'])): ?>
                <a class="<?= $isEventos ? 'active' : '' ?>" href="<?= e(url('/categoria/' . $catsBySlug['eventos-agenda']['slug'])) ?>">Eventos / Agenda</a>
            <?php endif; ?>
            <a class="<?= $isFofocas ? 'active' : '' ?>" href="<?= e(url('/fofocas')) ?>">Fofocas</a>
            <a class="<?= $isUltimas ? 'active' : '' ?>" href="<?= e(url('/ultimas')) ?>">Últimas</a>
            <a class="btn-small cta-send" href="<?= e(url('/enviar')) ?>">Envie sua notícia/evento</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php foreach (($flash ?? []) as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-info">
            <strong>BabadoVip</strong>
            <span>Colunista responsável: Lenaldo Santana</span>
            <span>Capela - Sergipe</span>
        </div>
        <div class="footer-actions">
            <a class="btn-small cta-contact" href="<?= e(url('/contato')) ?>">Fale conosco</a>
            <a class="btn-small vip" href="<?= e(url('/admin/login')) ?>">Admin</a>
        </div>
    </div>
</footer>
<div class="lgpd-popup" id="lgpdPopup" hidden>
    <div class="lgpd-popup-card" role="dialog" aria-modal="true" aria-labelledby="lgpdPopupTitle">
        <h3 id="lgpdPopupTitle">Aviso de Privacidade (LGPD)</h3>
        <p>
            Coletamos dados de acesso como IP, navegador, data/hora e pagina de origem para seguranca,
            prevencao de fraude e cumprimento legal.
        </p>
        <p>
            <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm" target="_blank" rel="noopener noreferrer">
                Leia a Lei Geral de Protecao de Dados (Lei n 13.709/2018)
            </a>
        </p>
        <button type="button" class="btn-primary" id="lgpdPopupAccept">Entendi</button>
    </div>
</div>
<div class="welcome-popup" id="welcomePopup" hidden>
    <div class="welcome-popup-card" role="dialog" aria-modal="true" aria-labelledby="welcomePopupTitle">
        <h3 style="color: red;" id="welcomePopupTitle">Bem vindo. Aqui você é notícia!</h3>
        <p>
            Envie sua notícia/matéria sobre seu evento, algo que aconteceu na cidade. Será publicada aqui no <strong>BabadoVip</strong> para toda a região ler e compartilhar! Fique à vontade para enviar suas notícias, eventos, fotos e vídeos. Estamos ansiosos para compartilhar as novidades da nossa cidade e região!
        </p>
        <div class="welcome-popup-actions">
            <a class="btn-primary" id="welcomePopupSend" href="https://www.babadovip.com.br/enviar">Enviar noticia</a>
            <button type="button" class="btn-small" id="welcomePopupDismiss">Agora nao</button>
        </div>
    </div>
</div>
<script src="<?= e(url('assets/js/app.js?v=' . $jsVer)) ?>" defer></script>
</body>
</html>
