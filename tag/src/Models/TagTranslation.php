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
        'custom_fields',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
