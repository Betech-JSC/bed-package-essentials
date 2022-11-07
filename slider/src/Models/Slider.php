<?php

namespace Jamstackvietnam\Slider\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use Jamstackvietnam\Core\Models\BaseModel;
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
        'started_at',
        'ended_at',
    ];

    public $translatedAttributes = [
        'title',
        'description',
        'link',
        'banner_thumbnail',
        'banner_mobile_thumbnail',
    ];

    public $casts = [
        'banner_thumbnail' => 'array',
        'banner_mobile_thumbnail' => 'array',
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }

    public function getFormattedPositionAttribute()
    {
        return config('slider.positions.' . $this->position . '.title');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeAvailable($query)
    {
        $query->whereDate('started_at', '<=', now())->whereDate('ended_at', '>=', now());
        $query->orWhereDate('started_at', '<=', now())->whereNull('ended_at');
        $query->orWhereDate('ended_at', '>=', now())->whereNull('started_at');
        $query->orWhereNull('ended_at')->whereNull('started_at');
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'banner_thumbnail' => $this->banner_thumbnail,
            'banner_mobile_thumbnail' => $this->banner_mobile_thumbnail,
        ];
    }

    public function scopeGetByPosition(Builder $query, $position)
    {
        $query->where('position', $position)
            ->available()
            ->orderBy('sort_position', 'desc')
            ->orderBy('started_at', 'desc')
            ->orderBy('id', 'desc');

        if(config('slider.positions.' . $position . '.banner')) {
            return $query?->first()->transform();
        }
        else {
            return $query->get()
                ->map(fn ($item) => $item->transform());
        }
    }
}
