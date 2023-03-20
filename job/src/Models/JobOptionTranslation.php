<?php

namespace JamstackVietnam\Job\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Sluggable;

class JobOptionTranslation extends BaseModel
{
    use HasFactory, Sluggable;

    public $timestamps = false;
    public $slugAttribute = 'title';

    public $fillable = [
        'locale',
        'title',
        'slug',
    ];

    public function jobOption()
    {
        return $this->belongsTo(JobOption::class);
    }
}
