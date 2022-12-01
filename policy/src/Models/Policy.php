<?php

namespace Jamstackvietnam\Policy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;

class Policy extends BaseModel
{
    use HasFactory, SoftDeletes, Translatable, SearchableTrait;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = ['translations'];

    public $fillable = [
        'position',
        'icon',
        'status',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'content',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',
    ];

    public function modelRules()
    {
        return [
            'all' => [
                'title' => 'required',
                'content' => 'required',
            ],
        ];
    }

    protected $searchable = [
        'columns' => [
            'policy_translations.title' => 10,
            'policy_translations.id' => 5,
            'policy_translations.slug' => 2,
        ],
        'joins' => [
            'policy_translations' => ['policy_translations.policy_id', 'policies.id'],
        ],
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        $urls = [];
        if ($this->status == self::STATUS_ACTIVE) {
            $urls[] = route("policies.show", [
                'slug' => $this->seo_slug ?? $this->slug
            ]);
        }
        return $urls;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function transform()
    {
        return [
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'content' => $this->content,
            'icon' => $this->icon,
        ];
    }
}
