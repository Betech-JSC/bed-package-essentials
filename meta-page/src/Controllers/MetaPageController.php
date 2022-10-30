<?php

namespace Jamstackvietnam\MetaPage\Controllers;

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

        return Inertia::render($this->folder() . '/Form', [
            'item' => $item,
            'rules' => $this->getModelRules(__FUNCTION__)
        ]);
    }

    public function filter($query)
    {
        request()->merge(['paginate' => false]);
        return $query;
    }
}
