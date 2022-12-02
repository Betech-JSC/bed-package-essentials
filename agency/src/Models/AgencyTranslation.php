<?php

namespace JamstackVietnam\Agency\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;

class AgencyTranslation extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'locale',
        'title',
        'location',
        'description',
        'phones'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
