<?php

namespace Jamstackvietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jamstackvietnam\Core\Models\BaseModel;
use Jamstackvietnam\Core\Models\Sluggable;

class PostTranslation extends BaseModel
{
    use HasFactory, Sluggable;

    public $timestamps = false;
    public $slugAttribute = 'title';

    public $fillable = [
        'slug',
        'locale',
        'title',
        'description',
        'content',

        'meta_title',
        'custom_slug',
        'meta_description'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
