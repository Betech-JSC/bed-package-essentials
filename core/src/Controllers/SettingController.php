<?php

namespace JamstackVietnam\Core\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\Setting;
use JamstackVietnam\Core\Traits\HasCrudActions;
use Illuminate\Support\Facades\Artisan;

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

        if (!config('core.setting.form.' . $id . '.enable', true)) {
            $id = config('core.setting.id_default', null);

            if (!empty($id)) {
                return redirect()->route('admin.settings.form', ['id' =>  $id]);
            }
            else {
                return redirect()->route(config('core.setting.route_default', 'admin.dashboard.index'));
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

        if (in_array($id, ['smtp', 'notification'])) {
            Artisan::call('queue:clear');
        }

        return $this->redirectBack('Lưu đối tượng thành công.');
    }
}
