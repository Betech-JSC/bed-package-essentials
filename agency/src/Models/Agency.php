<?php

namespace JamstackVietnam\Agency\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;

class Agency extends BaseModel
{
    use HasFactory, SoftDeletes, Translatable, Searchable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = ['translations'];

    public $fillable = [
        'position',
        'is_headquarter',
        'is_featured',
        'status',
        'link_google_map',
        'longitude',
        'latitude',
        'region',
        'image',
    ];

    public $translatedAttributes = [
        'locale',
        'title',
        'location',
        'description',
        'open_time',
        'phones',
        'info'
    ];

    protected $casts = [
        'image' => 'array'
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'location' => 'required',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'latitude' => 'nullable|numeric|min:-90|max:90',
        ];
    }

    protected $searchable = [
        'columns' => [
            'agency_translations.title' => 10,
            'agency_translations.locale' => 10,
            'agency_translations.id' => 1,
        ],
        'joins' => [
            'agency_translations' => ['agency_translations.agency_id', 'agencies.id'],
        ],
    ];

    protected static function booted()
    {
        static::saved(function (self $model) {
            if (request()->route() === null) return;

            if($model->status == self::STATUS_ACTIVE && $model->is_headquarter) {
                self::active()
                    ->where('is_headquarter', true)
                    ->where('id', '<>', $model->id)
                    ->update(['is_headquarter' => false]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function transform()
    {
        return [
            'title' => $this->title,
            'location' => $this->location,
            'phones' => $this->phones,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'link_google_map' => $this->link_google_map,
            'image' => [
                'url' => isset($this->image['path']) ? static_url($this->image['path']) : null,
                'alt' => $this->image['alt'] ?? $this->title,
            ]
        ];
    }

    public function transformDetails()
    {
        return [
            'title' => $this->title,
            'location' => $this->location,
            'phones' => $this->phones,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'link_google_map' => $this->link_google_map,
            'description' => $this->description,
            'open_time' => $this->open_time,
            'info' => $this->info,
            'region' => $this->region,
            'image' => [
                'url' => isset($this->image['path']) ? static_url($this->image['path']) : null,
                'alt' => $this->image['alt'] ?? $this->title,
            ]
        ];
    }
}
