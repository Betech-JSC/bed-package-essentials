<?php

namespace JamstackVietnam\Slider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;

class SliderTranslation extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'title',
        'description',
        'link',
        'target',
        'image',
        'image_mobile',
        'customize'
    ];

    public $casts = [
        'image_mobile' => 'array',
        'image' => 'array',
        'customize' => 'array'
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
