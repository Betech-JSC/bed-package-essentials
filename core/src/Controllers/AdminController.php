<?php

namespace JamstackVietnam\Core\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\Core\Traits\HasCrudActions;

class AdminController extends Controller
{
    use HasCrudActions;

    public $model = Admin::class;

    public $appends = [
        'index' => ['role_names'],
        'form' => ['role']
    ];

    private function folder()
    {
        return "@Core/" . Str::studly($this->getTable());
    }

    private function beforeStore($request, $rules)
    {
        if (!$request->input('id')) {
            $request->validate([
                'role' => 'required',
                'password' => 'required|min:8|max:255|confirmed',
            ]);
        }

        return $rules;
    }

    private function afterForm($item)
    {
        if (!current_admin()->hasRole('admin') && $item->hasRole('admin')) {
            abort(403);
        }

        return $item;
    }

    private function beforeIndex($query)
    {
        $query->orderBy('id', 'DESC');

        if (current_admin()->hasRole('admin')) {
            $query->whereHas('roles', function ($query) {
                $query->whereNot('name', 'admin');
            });
        }

        return $query;
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'nullable|min:8|max:255|confirmed',
        ]);

        $data = [
            'name' => $request->input('name'),
        ];

        if ($password = $request->input('password')) {
            $data['password'] = Hash::make($password);
        }

        auth('admin')->user()->update($data);
    }
}
