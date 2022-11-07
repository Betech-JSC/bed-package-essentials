<?php

namespace Jamstackvietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jamstackvietnam\Core\Models\BaseModel;

class SliderTranslation extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'title',
        'description',
        'link',
        'banner_thumbnail_url',
        'banner_mobile_thumbnail_url',
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
