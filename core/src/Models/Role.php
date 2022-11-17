<?php

namespace JamstackVietnam\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
