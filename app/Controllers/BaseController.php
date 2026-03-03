<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Core\View;
use App\Models\CategoryModel;
use App\Models\SiteVisitModel;

abstract class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'public'): void
    {
        $this->trackSiteVisit($layout);
        $data['flash'] = Flash::all();
        $data['menuCategories'] = (new CategoryModel())->allActive();
        echo View::render($view, $data, $layout);
    }

    protected function sanitizeRichText(string $html): string
    {
        return strip_tags($html, '<p><br><strong><em><ul><ol><li><a><blockquote><h2><h3>');
    }

    private function trackSiteVisit(string $layout): void
    {
        if ($layout !== 'public') {
            return;
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            return;
        }
        if (!empty($_SESSION['_site_visit_counted'])) {
            return;
        }

        try {
            (new SiteVisitModel())->recordVisit();
            $_SESSION['_site_visit_counted'] = 1;
        } catch (\Throwable) {
            // Analytics must not break page rendering.
        }
    }
}
