<?php

namespace JamstackVietnam\Core\Models;

use Illuminate\Database\Eloquent\Model;
use JamstackVietnam\Core\Traits\ResponseCache;
use JamstackVietnam\Core\Traits\HasRichText;

/**
 * Class BaseModel
 * @package App\Models
 */
class BaseModel extends Model
{
    use ResponseCache;
    use HasRichText;

    public function getFormattedUpdatedAtAttribute(): string
    {
        return to_date($this->attributes['updated_at'], 'd/m/Y');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return to_date($this->attributes['created_at'], 'd/m/Y');
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderByRaw('ISNULL(position) OR position = 0, position ASC')
            ->orderByDesc('id');
    }

    public function scopeOrderByFeatured($query)
    {
        return $query->orderBy('is_featured', 'desc')
            ->orderByDesc('id');
    }
}
