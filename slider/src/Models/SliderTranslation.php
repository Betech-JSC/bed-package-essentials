<?php

namespace JamstackVietnam\Blog\Models;

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
    ];

    public $casts = [
        'image_mobile' => 'array',
        'image' => 'array',
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
