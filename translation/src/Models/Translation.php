<?php

namespace JamstackVietnam\Translation\Models;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    public $fillable = [
        'locale',
        'key',
        'value',
    ];

    public $rules = [
        'locale' => 'required',
        'key' => 'required',
        'value' => 'required',
    ];

    public $appends = ['cover_value'];

    public function getCoverValueAttribute()
    {
        return str_replace('\x40', '@', $this->value);
    }
}
