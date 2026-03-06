<?php
declare(strict_types=1);

namespace App\Services;

final class UploadService
{
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
    private const MIME_TO_EXT = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
    ];

    public function processMultiple(array $files, string $targetDir, int $maxFiles = 20): array
    {
        $normalized = $this->normalizeFiles($files);
        if (count($normalized) > $maxFiles) {
            throw new \RuntimeException('Limite de ' . $maxFiles . ' fotos excedido.');
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Falha ao criar diretório de upload.');
        }

        $saved = [];
        foreach ($normalized as $item) {
            if (($item['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($item['error'] !== UPLOAD_ERR_OK) {
                throw new \RuntimeException('Erro no upload de arquivo.');
            }
            if (($item['size'] ?? 0) > (int) config('security.max_upload_size')) {
                throw new \RuntimeException('Arquivo excede o limite permitido.');
            }

            $tmp = $item['tmp_name'];
            $mime = (string) mime_content_type($tmp);
            $ext = self::MIME_TO_EXT[$mime] ?? '';
            if (!in_array($mime, self::ALLOWED_MIME, true) || !in_array($ext, self::ALLOWED_EXT, true)) {
                throw new \RuntimeException('Tipo de arquivo não permitido.');
            }

            $name = bin2hex(random_bytes(16)) . '.' . $ext;
            $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $name;
            if (!move_uploaded_file($tmp, $dest)) {
                throw new \RuntimeException('Falha ao salvar arquivo.');
            }

            $saved[] = ['name' => $name, 'full_path' => $dest];
        }

        return $saved;
    }

    public function createThumbnail(string $sourcePath, string $thumbPath, int $maxWidth = 400): void
    {
        $info = getimagesize($sourcePath);
        if ($info === false) {
            return;
        }
        [$width, $height] = $info;
        if ($width <= $maxWidth) {
            copy($sourcePath, $thumbPath);
            return;
        }
        $ratio = $height / $width;
        $newWidth = $maxWidth;
        $newHeight = (int) round($newWidth * $ratio);

        $srcImg = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            'image/avif' => function_exists('imagecreatefromavif') ? @imagecreatefromavif($sourcePath) : null,
            default => null,
        };
        if (!$srcImg) {
            @copy($sourcePath, $thumbPath);
            return;
        }
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $ext = strtolower((string) pathinfo($thumbPath, PATHINFO_EXTENSION));
        if ($ext === 'avif' && !function_exists('imageavif')) {
            @copy($sourcePath, $thumbPath);
            imagedestroy($srcImg);
            imagedestroy($thumb);
            return;
        }

        match ($ext) {
            'jpg', 'jpeg' => imagejpeg($thumb, $thumbPath, 82),
            'png' => imagepng($thumb, $thumbPath, 7),
            'webp' => imagewebp($thumb, $thumbPath, 82),
            'avif' => imageavif($thumb, $thumbPath, 70),
            default => imagejpeg($thumb, $thumbPath, 82),
        };

        imagedestroy($srcImg);
        imagedestroy($thumb);
    }

    public function createSocialCard(string $sourcePath, string $targetPath, int $targetWidth = 1200, int $targetHeight = 630): void
    {
        $info = @getimagesize($sourcePath);
        if ($info === false) {
            return;
        }

        $srcImg = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            'image/avif' => function_exists('imagecreatefromavif') ? @imagecreatefromavif($sourcePath) : null,
            default => null,
        };
        if (!$srcImg) {
            return;
        }

        $srcWidth = (int) ($info[0] ?? 0);
        $srcHeight = (int) ($info[1] ?? 0);
        if ($srcWidth <= 0 || $srcHeight <= 0) {
            imagedestroy($srcImg);
            return;
        }

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $white);

        $srcRatio = $srcWidth / $srcHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($srcRatio > $targetRatio) {
            $cropHeight = $srcHeight;
            $cropWidth = (int) round($srcHeight * $targetRatio);
            $srcX = (int) round(($srcWidth - $cropWidth) / 2);
            $srcY = 0;
        } else {
            $cropWidth = $srcWidth;
            $cropHeight = (int) round($srcWidth / $targetRatio);
            $srcX = 0;
            $srcY = (int) round(($srcHeight - $cropHeight) / 2);
        }

        imagecopyresampled(
            $target,
            $srcImg,
            0,
            0,
            $srcX,
            $srcY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        imagejpeg($target, $targetPath, 85);

        imagedestroy($srcImg);
        imagedestroy($target);
    }

    public function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        $items = scandir($path) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $full = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($full)) {
                $this->deleteDirectory($full);
            } else {
                @unlink($full);
            }
        }
        @rmdir($path);
    }

    private function normalizeFiles(array $files): array
    {
        $normalized = [];
        if (!isset($files['name'])) {
            return $normalized;
        }
        if (is_array($files['name'])) {
            foreach (array_keys($files['name']) as $i) {
                $normalized[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i] ?? '',
                    'tmp_name' => $files['tmp_name'][$i] ?? '',
                    'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $files['size'][$i] ?? 0,
                ];
            }
            return $normalized;
        }
        $normalized[] = $files;
        return $normalized;
    }
}
