<?php

namespace JamstackVietnam\Redirect\Controllers;

use App\Http\Controllers\Controller;
use JamstackVietnam\Redirect\Models\Redirect;
use JamstackVietnam\Core\Traits\HasCrudActions;

class RedirectController extends Controller
{
    use HasCrudActions;

    public $model = Redirect::class;
}
