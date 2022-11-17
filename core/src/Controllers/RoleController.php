<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use JamstackVietnam\Core\Models\Role;
use JamstackVietnam\Core\Traits\HasCrudActions;

class RoleController extends Controller
{
    use HasCrudActions;

    public $model = Role::class;

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }
}
