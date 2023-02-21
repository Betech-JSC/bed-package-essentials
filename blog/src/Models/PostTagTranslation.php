<?php

namespace JamstackVietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Sluggable;

class PostTagTranslation extends BaseModel
{
    use HasFactory, Sluggable;

    public $timestamps = false;
    public $slugAttribute = 'title';

    public $fillable = [
        'locale',
        'title',
        'slug',
        'description',
    ];

    public function tag()
    {
        return $this->belongsTo(PostTag::class);
    }
}
