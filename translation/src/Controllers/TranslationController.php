<?php

namespace Jamstackvietnam\Translation\Controllers;

use App\Http\Controllers\Controller;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\Translation\Models\Translation;

class TranslationController extends Controller
{
    use HasCrudActions;

    public $model = Translation::class;

    public function beforeIndex($query)
    {
        $pages = Translation::all();

        $storedRoutes = $pages->pluck('url');
        $routes = collect(Sitemap::create()->addStaticRoutes()->tags)
            ->transform(
                fn ($item) =>
                str_replace(env('APP_URL'), '', $item['url']) ?: '/'
            );

        $diff = $routes->diff($storedRoutes);

        if ($diff->count()) {
            Translation::insert($diff->transform(fn ($item) => ['url' => $item])->toArray());
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
