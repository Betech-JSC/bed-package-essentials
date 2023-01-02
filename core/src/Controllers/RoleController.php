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
