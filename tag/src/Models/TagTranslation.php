<?php

namespace JamstackVietnam\Tag\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Sluggable;

class TagTranslation extends BaseModel
{
    use HasFactory, Sluggable;

    public $timestamps = false;
    public $slugAttribute = 'title';

    public $fillable = [
        'locale',
        'title',
        'slug',
        'description',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}