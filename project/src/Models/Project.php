<?php

namespace JamstackVietnam\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;
use \Illuminate\Support\Facades\Route;

class Project extends BaseModel
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
        'status',
        'view_count',
        'image',
        'condition',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'description',
        'content',
        'type',
        'progress',
        'used',
        'location',

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
        'image' => 'array',
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required',
        ];
    }

    protected $searchable = [
        'columns' => [
            'project_translations.title' => 10,
            'project_translations.id' => 5,
            'project_translations.slug' => 2,
        ],
        'joins' => [
            'project_translations' => ['project_translations.project_id', 'projects.id'],
        ],
    ];

    protected $appends = ['url'];

    protected static function booted()
    {
        static::saved(function (self $model) {
            if (request()->route() === null) return;
            $model->saveRelatedProject($model);

            if (request()->has('categories')) {
                $model->saveCategories($model);
            }
        });
    }

    public function saveCategories($model)
    {
        $categories = array_column(request()->input('categories', []), 'id');
        $model->categories()->sync($categories, 'id');
    }

    public function saveRelatedProject($model)
    {
        $relatedProjects = array_column(request()->input('related_projects', []), 'id');
        $model->relatedProjects()->sync($relatedProjects, 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            ProjectCategory::class,
            'project_ref_categories',
            'project_id',
            'project_category_id'
        );
    }

    public function relatedProjects()
    {
        return $this->belongsToMany(
            self::class,
            'related_projects',
            'project_id',
            'related_project_id'
        );
    }

    public function getUrlAttribute()
    {
        $urls = [];
        $default_locale = config('app.locale');
        if ($this->is_active) {
            if (Route::has($default_locale . ".projects.show")) {
                foreach ($this->translations as $translation) {
                    $urls[strtoupper($translation->locale)] = route("$translation->locale.projects.show", [
                        'slug' => $translation->seo_slug ?? $translation->slug
                    ]);
                }
            }
        }
        return $urls;
    }

    public function scopeActive($query)
    {
        return $query->whereLocaleActive()->where('status', self::STATUS_ACTIVE);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function transform()
    {
        return [
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'type' => $this->type,
            'progress' => $this->progress,
            'used' => $this->used,
            'location' => $this->location,
            'condition' => $this->condition,
            'image' => $this->getImageDetail($this->image),
        ];
    }

    public function transformDetails()
    {
        return [
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'content' => transform_richtext($this->content),
            'type' => $this->type,
            'progress' => $this->progress,
            'used' => $this->used,
            'location' => $this->location,
            'condition' => $this->condition,
            'image' => $this->getImageDetail($this->image),
        ];
    }

    public function getImageDetail($image)
    {
        return [
            'url' => isset($image['path']) ? static_url($image['path']) : null,
            'alt' => $image['alt'] ?? $this->title,
        ];
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function scopeSortByPosition($query)
    {
        return $query->orderByRaw('ISNULL(position) OR position = 0, position ASC')
            ->orderBy('id', 'desc');
    }
}
