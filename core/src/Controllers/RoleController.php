<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\Role;
use JamstackVietnam\Core\Traits\HasCrudActions;
use Silber\Bouncer\BouncerFacade;

class RoleController extends Controller
{
    use HasCrudActions;

    public $model = Role::class;

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }

    private function afterForm($item)
    {
        $actions = [];
        $locale = config('app.locale');
        $abilities = $item->getAbilities();
        dd($abilities);

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $action = $route->getAction();
            $name = $route->getName();

            if (
                $name && isset($action['controller']) &&
                (Str::startsWith($name, 'admin.') ||
                    Str::startsWith($name, "$locale.admin.")) &&
                !Str::startsWith($name, 'admin.helper') &&
                !str_contains($action['controller'], 'Controllers\Auth')
            ) {
                $fullAction = str_replace("$locale.admin.", "admin.", $name);

                $tables = explode('.', $fullAction)[1];
                $action = str_replace("admin.", "", $fullAction);

                $actions[$tables][$fullAction] = $abilities->can($fullAction);
            }
        }

        return [
            ...$item->toArray(),
            'permissions' => Role::getActions()
        ];
    }

    private function afterStore($request, $item)
    {
        foreach ($request->input('permissions') as $actions) {
            foreach ($actions as $action => $actionKey) {
                if ($actionKey) {
                    BouncerFacade::allow($item->name)->to($action);
                } else {
                    BouncerFacade::disallow($item->name)->to($action);
                }
            }
        }
    }
}
