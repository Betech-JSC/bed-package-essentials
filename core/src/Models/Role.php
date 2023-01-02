<?php

namespace JamstackVietnam\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade;

class Role extends Model
{
    use HasFactory;

    public $fillable = [
        'title'
    ];

    public $rules = [
        'title' => 'required|max:255',
    ];

    public static function getRoles()
    {
        $query = self::query();
        if (auth('admin')->user()->isNotAn('admin')) {
            $query->whereNot('name', 'admin');
        }
        return $query->get();
    }

    public static function getActions()
    {
        $actions = [];
        $locale = config('app.locale');

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

                $actions[$tables][$fullAction] = BouncerFacade::can($fullAction);
            }
        }
        return $actions;
    }
}
