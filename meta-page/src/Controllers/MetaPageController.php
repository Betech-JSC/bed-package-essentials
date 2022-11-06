<?php

namespace Jamstackvietnam\MetaPage\Controllers;

use App\Http\Controllers\Controller;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\MetaPage\Models\MetaPage;
use Jamstackvietnam\Sitemap\Sitemap;

class MetaPageController extends Controller
{
    use HasCrudActions;

    public $model = MetaPage::class;

    public function beforeIndex($query)
    {
        $storedRoutes = MetaPage::pluck('url');
        $routes = collect(Sitemap::create()->addStaticRoutes()->tags)
            ->transform(
                fn ($item) =>
                str_replace(env('APP_URL'), '', $item['url']) ?: '/'
            );

        $diff = $routes->diff($storedRoutes);

        if ($diff->count()) {
            MetaPage::insert($diff->transform(fn ($item) => ['url' => $item])->toArray());
        }

        return $query->orderBy('url', 'ASC');
    }

    private function transform($item)
    {
        return [
            ...$item->toArray(),
            'url' => env('APP_URL') . $item['url']
        ];
    }
}
