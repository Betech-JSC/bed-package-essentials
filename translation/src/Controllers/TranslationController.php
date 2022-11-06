<?php

namespace Jamstackvietnam\Translation\Controllers;

use App\Http\Controllers\Controller;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\Translation\Models\Translation;

class TranslationController extends Controller
{
    use HasCrudActions;

    public $model = Translation::class;
}
