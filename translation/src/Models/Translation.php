<?php

namespace Jamstackvietnam\Translation\Models;

class Translation
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
}
