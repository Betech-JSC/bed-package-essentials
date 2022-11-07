<?php

namespace Jamstackvietnam\Slider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use Jamstackvietnam\Support\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Jamstackvietnam\Core\Models\Translatable;

class Slider extends BaseModel
{
    use HasFactory, SoftDeletes, SearchableTrait, Translatable;

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Hiển thị',
        self::STATUS_INACTIVE => 'Ẩn',
    ];

    public $with = ['translations'];

    protected $searchable = [
        'columns' => [
            'slider_translations.title' => 10,
        ],
        'joins' => [
            'slider_translations' => ['slider_translations.slider_id', 'sliders.id'],
        ],
    ];

    public $fillable = [
        'status',
        'position',
        'sort_position',

    ];

    public $translatedAttributes = [
        'title',
        'description',
        'link',
        'banner_thumbnail_url',
        'banner_mobile_thumbnail_url',
    ];

    public function modelRules()
    {
        return [
            'all' => [
                'title' => 'required|string|max:255',
            ],
        ];
    }

    protected static function booted()
    {
        static::saved(function (self $model) {
            if (request()->route() === null) return;
            if ($model->status == self::STATUS_ACTIVE && config('slider.positions.' . $model->position . '.count') == 1) {
                self::where('id', '<>', $model->id)
                    ->where('status', self::STATUS_ACTIVE)
                    ->where('position', $model->position)
                    ->update(['status' => self::STATUS_INACTIVE]);
            }
        });
    }

    public function getFormattedPositionAttribute()
    {
        return config('slider.positions.' . $this->position . '.title');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'banner_thumbnail' => $this->banner_thumbnail_url,
            'banner_mobile_thumbnail' => $this->banner_mobile_thumbnail_url,
        ];
    }

    public function scopeGetByPosition(Builder $query, $position)
    {
        $query->where('position', $position)
            ->orderBy('sort_position', 'desc')
            ->orderBy('id', 'desc');

        $length = config('slider.positions.' . $position . '.count');

        if($length == 1) {
            return $query->first()->transform();
        }
        else {
            return $query->take($length)
                ->get()
                ->map(fn ($item) => $item->transform());
        }
    }
}
