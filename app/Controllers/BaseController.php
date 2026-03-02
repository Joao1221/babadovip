<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Core\View;
use App\Models\CategoryModel;

abstract class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'public'): void
    {
        $data['flash'] = Flash::all();
        $data['menuCategories'] = (new CategoryModel())->allActive();
        echo View::render($view, $data, $layout);
    }

    protected function sanitizeRichText(string $html): string
    {
        return strip_tags($html, '<p><br><strong><em><ul><ol><li><a><blockquote><h2><h3>');
    }
}
