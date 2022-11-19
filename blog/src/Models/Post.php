<?php

namespace JamstackVietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;

class Post extends BaseModel
{
    use HasFactory, SoftDeletes, Searchable, Translatable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = ['translations', 'categories'];

    public $fillable = [
        'status',
        'published_at',
        'is_home',
        'is_featured',
        'view_count',
        'image',

        'inject_head',
        'inject_body_start',
        'inject_body_end',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'description',
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

    protected $casts = [
        'image' => 'array'
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }

    protected $searchable = [
        'columns' => [
            'post_translations.title' => 10,
            'post_translations.id' => 5,
            'post_translations.slug' => 5,
        ],
        'joins' => [
            'post_translations' => ['post_translations.post_id', 'posts.id'],
        ],
    ];

    protected $appends = ['url'];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (request()->route() === null) return;
            $model->published_at = request()->input('published_at', now());
        });

        static::saved(function (self $model) {
            if (request()->route() === null) return;
            $model->saveRelatedPosts($model);
            $model->saveCategories($model);
        });
    }

    public function saveCategories($model)
    {
        $categories = array_column(request()->input('categories', []), 'id');
        $model->categories()->sync($categories, 'id');
    }

    public function saveRelatedPosts($model)
    {
        $relatedPosts = array_column(request()->input('post_related_ids', []), 'id');
        $model->relatedPosts()->sync($relatedPosts, 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            PostCategory::class,
            'post_ref_categories',
            'post_id',
            'post_category_id'
        );
    }

    public function relatedPosts()
    {
        return $this->belongsToMany(
            self::class,
            'related_posts',
            'post_id',
            'related_post_id'
        );
    }

    public function getPostRelatedIdsAttribute()
    {
        return $this->relatedPosts;
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        if ($this->is_active) {
            foreach ($this->translations as $translation) {
                $urls[strtoupper($translation->locale)] = route("$translation->locale.posts.show", [
                    'slug' => $translation->seo_slug ?? $translation->slug,
                    'id' => $this->id,
                ]);
            }
        }
        return $urls;
    }

    public function getCategoryAttribute()
    {
        return $this->categories->first()?->transform();
    }

    public function scopeActive($query)
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where('published_at', '<=', now());
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE &&
            $this->published_at <= now();
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'updated_at' => $this->formatted_updated_at,
            'formatted_created_at' => $this->formatted_created_at,
            'description' => $this->description,
            'category' => $this->category,
            'image' => [
                'url' => static_url($this->image['url'] ?? null),
                'alt' => $this->image['alt'] ?? $this->title,
            ]
        ];
    }

    public function transformDetails()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'updated_at' => $this->formatted_updated_at,
            'formatted_created_at' => $this->formatted_created_at,
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'categories' => $this->categories->map(fn ($item) => $item->transform()),
            'image' => [
                'url' => static_url($this->image['url'] ?? null),
                'alt' => $this->image['alt'] ?? $this->title,
            ]
        ];
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public static function postByPosition($position)
    {
        return self::query()
            ->active()
            ->orderBy('is_featured', 'desc')
            ->orderBy('id', 'desc')
            ->whereHas('categories', function ($query) use ($position) {
                $query->active()->where('post_categories.position', $position);
            })
            ->take(8)
            ->get()
            ->map(fn ($items) => $items->transform());
    }

    public function related($limit = 8)
    {
        $relatedPosts = $this->relatedPosts
            ->where('status', self::STATUS_ACTIVE)->values()
            ->take($limit);

        $relatedPostIds = [];

        if ($relatedPosts->count() > 0) {
            $relatedPostIds = $relatedPosts->pluck('id');
        }

        if (count($relatedPosts) < $limit) {
            $addPosts = self::query()
                ->active()
                ->when($this->category['id'] ?? false, function ($query) {
                    $query->whereHas('categories', function ($query) {
                        $query->where('post_categories.id', $this->category['id']);
                    });
                })
                ->where('id', '<>', $this->id)
                ->whereNotIn('id', $relatedPostIds)
                ->take($limit - count($relatedPosts))
                ->get();

            if (count($addPosts) > 0) {
                $relatedPosts = $relatedPosts->concat($addPosts);
            }
        }

        return $relatedPosts->map(fn ($item) => $item->transform());
    }
}
