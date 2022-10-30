<?php

namespace Jamstackvietnam\MetaPage\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\MetaPage\Models\MetaPage;

class MetaPageController extends Controller
{
    use HasCrudActions;

    public $model = MetaPage::class;

    public function table()
    {
        $items = MetaPage::getAll();

        return response()->json($items);
    }

    public function form()
    {
        $url = request()->input('id') ?? '';
        $item = MetaPage::where('url', $url)->firstOrFail();

        $breadcrumbs = [[
            'url' => route($this->getResource() . '.index'),
            'name' => trans('models.table_list.' . $this->getTable()),
        ]];

        if (request()->wantsJson()) {
            return response()->json($item);
        }

        return Inertia::render($this->folder() . '/Form', [
            'item' => $item,
            'breadcrumbs' => $breadcrumbs,
            'schema' => $this->getSchema(),
        ]);
    }
}
