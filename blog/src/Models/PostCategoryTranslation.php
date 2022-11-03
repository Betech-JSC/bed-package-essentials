<?php

namespace Jamstackvietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jamstackvietnam\Core\Models\BaseModel;
use Jamstackvietnam\Core\Models\Sluggable;

class PostCategoryTranslation extends BaseModel
{
    use HasFactory, Sluggable;

    public $timestamps = false;
    public $slugAttribute = 'title';

    public $fillable = [
        'slug',
        'locale',
        'title',

        'meta_title',
        'custom_slug',
        'meta_description'
    ];

    public function category()
    {
        return $this->belongsTo(PostCategory::class);
    }
}
