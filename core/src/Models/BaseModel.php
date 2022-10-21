<?php

namespace Jamstackvietnam\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Jamstackvietnam\Core\Traits\ResponseCache;

/**
 * Class BaseModel
 * @package App\Models
 */
class BaseModel extends Model
{
    use ResponseCache;

    public function getFormattedUpdatedAtAttribute(): string
    {
        return to_date($this->attributes['updated_at'], 'd/m/Y');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return to_date($this->attributes['created_at'], 'd/m/Y');
    }
}
