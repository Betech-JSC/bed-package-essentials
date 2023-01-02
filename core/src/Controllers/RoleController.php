<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\Role;
use JamstackVietnam\Core\Traits\HasCrudActions;
use Illuminate\Support\Facades\Route;

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
        $rolePermissions = $item->permissions->pluck('name')->toArray();

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

                $actions[$tables][$fullAction] = in_array($fullAction, $rolePermissions);
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
            dd($actions);
            // $role->syncPermissions($permissions);
            // foreach ($actions as $action => $actionKey) {
            //     if ($actionKey) {
            //         $item->revokePermissionTo($action);
            //         // BouncerFacade::allow($item->name)->to($action);
            //     } else {
            //         $item->revokePermissionTo($action);
            //         // BouncerFacade::disallow($item->name)->to($action);
            //     }
            // }
        }
    }
}
