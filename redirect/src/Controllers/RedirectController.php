<?php

namespace Jamstackvietnam\Redirect\Controllers;

use App\Http\Controllers\Controller;
use Jamstackvietnam\Redirects\Models\Redirect;
use Jamstackvietnam\Core\Traits\HasCrudActions;

class RedirectController extends Controller
{
    use HasCrudActions;

    public $model = Redirect::class;
}