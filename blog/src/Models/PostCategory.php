<?php

namespace JamstackVietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use JamstackVietnam\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;

class PostCategory extends BaseModel
{
    use HasFactory, Translatable, SoftDeletes, Searchable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const POSITION_HOMEPAGE = 'HOMEPAGE';
    public const POSITION_JOURNEY = 'JOURNEY';
    public const POSITION_STRATEGY = 'STRATEGY';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'ACTIVE',
        self::STATUS_INACTIVE => 'INACTIVE',
    ];

    public const POSITION_LIST = [
        self::POSITION_HOMEPAGE => 'Trang chủ',
        self::POSITION_JOURNEY => 'Hành trình kiến tạo giá trị',
        self::POSITION_STRATEGY => 'Chiến lược nhân sự',
    ];

    public $with = ['translations'];

    protected $appends = ['url'];

    public $fillable = [
        'status',
        'position',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',
    ];

    protected $searchable = [
        'columns' => [
            'post_category_translations.title' => 10,
            'post_category_translations.id' => 5,
            'post_category_translations.slug' => 5,
        ],
        'joins' => [
            'post_category_translations' => ['post_category_translations.post_category_id', 'post_categories.id'],
        ],
    ];

    public function modelRules(): array
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
            if ($model->status == self::STATUS_ACTIVE && !empty($model->position)) {
                self::where('id', '<>', $model->id)
                    ->where('position', $model->position)
                    ->update(['position' => null]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        if($this->status == self::STATUS_ACTIVE) {
            foreach ($this->translations as $translation) {
                $urls[strtoupper($translation->locale)] = route("$translation->locale.posts.category", [
                    'slug' => $translation->seo_slug ?? $translation->slug,
                ]);
            }
        }
        return $urls;
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
        ];
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function transformPosts()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'posts' => $this->posts(),

            'meta_title' => $this->meta_title ?? $this->title,
            'meta_description' => $this->meta_description ?? $this->description
        ];
    }

    public function posts()
    {
        return Post::query()
            ->active()
            ->where('published_at', '<=', now())
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->whereHas('categories', function ($query) {
                $query->where('post_categories.id', $this->id);
            })
            ->take(6)
            ->get()
            ->map(fn ($item) => $item->transform());
    }
}
