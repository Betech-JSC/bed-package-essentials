<?php

namespace Jamstackvietnam\MetaPage\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\MetaPage\Models\MetaPage;
use Jamstackvietnam\Sitemap\Sitemap;

class MetaPageController extends Controller
{
    use HasCrudActions;

    public $model = MetaPage::class;


    public function beforeIndex()
    {
        $pages = MetaPage::all();

        $storedRoutes = $pages->pluck('url');
        $routes = collect(Sitemap::create()->addStaticRoutes()->tags)
            ->transform(
                fn ($item) =>
                str_replace(env('APP_URL'), '', $item['url'])
            );

        $diff = $routes->diff($storedRoutes);

        if ($diff->count()) {
            self::insert($diff->transform(fn ($item) => ['url' => $item])->toArray());
        }
    }

    // public function table()
    // {
    //     $items = MetaPage::getAll();

    //     return response()->json($items);
    // }

    // public function form()
    // {
    //     $url = request()->input('id') ?? '';
    //     $item = MetaPage::where('url', $url)->firstOrFail();

    //     $breadcrumbs = [[
    //         'url' => route($this->getResource() . '.index'),
    //         'name' => trans('models.table_list.' . $this->getTable()),
    //     ]];

    //     if (request()->wantsJson()) {
    //         return response()->json($item);
    //     }

    //     return Inertia::render($this->folder() . '/Form', [
    //         'item' => $item,
    //         'breadcrumbs' => $breadcrumbs,
    //         'schema' => $this->getSchema(),
    //     ]);
    // }
}
