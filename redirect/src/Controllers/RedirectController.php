<?php

namespace JamstackVietnam\Redirect\Controllers;

use App\Http\Controllers\Controller;
use JamstackVietnam\Redirect\Models\Redirect;
use JamstackVietnam\Core\Traits\HasCrudActions;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    use HasCrudActions;

    public $model = Redirect::class;

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }

    public function index()
    {
        $this->checkAuthorize();

        if (request()->wantsJson()) {
            return $this->table();
        }

        return Inertia::render($this->folder() . '/Index', [
            'breadcrumbs' => [
                [
                    'url' => route($this->getResource() . '.index'),
                    'name' => 'models.table_list.' . $this->getTable(),
                ]
            ],
            'schema' => $this->getSchema(),
            'configs' => setting_bar()
        ]);
    }
}
