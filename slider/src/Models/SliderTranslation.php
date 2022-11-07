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
        'target',
        'banner_thumbnail',
        'banner_mobile_thumbnail',
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}
