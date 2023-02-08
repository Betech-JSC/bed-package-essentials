<?php

namespace JamstackVietnam\Core\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\Setting;
use JamstackVietnam\Core\Traits\HasCrudActions;

class SettingController extends Controller
{
    use HasCrudActions;

    public $model = Setting::class;

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }

    public function form($id = null)
    {
        $this->checkAuthorize($id ? 'edit' : 'create');

        if (!!config('core.setting.form.' . $id . '.disable')) {
            foreach(config('core.setting.form') as $key => $value) {
                if (!$value['disable']) {
                    $id = $key;
                }
            }
        }

        $settingName =  Str::studly($id);

        $breadcrumbs = [
            [
                'url' => route($this->getResource() . '.index'),
                'name' => trans('models.table_list.' . $this->getTable()),
            ],
            [
                'url' => route($this->getResource() . '.form', ['id' => $id]),
                'name' => $settingName,
            ]
        ];

        $item = settings()->group($id)->all();

        if (request()->wantsJson()) {
            return response()->json($item);
        }

        return Inertia::render($this->folder() . '/' . $settingName, [
            'item' => $item,
            'breadcrumbs' => $breadcrumbs,
            'schema' => $this->getSchema(),
            'setting_bar' => setting_bar()
        ]);
    }

    public function store(Request $request, $id = null)
    {
        $this->checkAuthorize($id ? 'update' : 'store');

        $rules = $this->model::rules($id);

        $validated = $request->validate($rules);

        settings()->group($id)->set($validated);

        return $this->redirectBack('Lưu đối tượng thành công.');
    }
}
