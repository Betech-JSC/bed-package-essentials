<?php

namespace JamstackVietnam\Job\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;
use \Illuminate\Support\Facades\Route;

class Job extends BaseModel
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
        'expected_time',
        'published_at',
        'status',
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'description',
        'content',
        'working_position',
        'workplace',
        'working_form',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',
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
            'job_translations.title' => 10,
            'job_translations.id' => 5,
            'job_translations.slug' => 2,
        ],
        'joins' => [
            'job_translations' => ['job_translations.job_id', 'jobs.id'],
        ],
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        $urls = [];
        $default_locale = config('app.locale');
        if ($this->is_active) {
            if (Route::has($default_locale . ".jobs.show")) {
                foreach ($this->translations as $translation) {
                    $urls[strtoupper($translation->locale)] = route("$translation->locale.jobs.show", [
                        'slug' => $translation->seo_slug ?? $translation->slug
                    ]);
                }
            }
        }
        return $urls;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('published_at', '<=', now());
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE && $this->published_at <= now();
    }

    public function transform()
    {
        return [
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'working_position' => $this->working_position,
            'workplace' => $this->workplace,
            'working_form' => $this->working_form,
            'expected_time' => $this->expected_time,
            'count' => $this->count,
        ];
    }

    public function transformDetails()
    {
        return [
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'content' => transform_richtext($this->content),
            'working_position' => $this->working_position,
            'workplace' => $this->workplace,
            'working_form' => $this->working_form,
            'expected_time' => $this->expected_time,
            'published_at' => $this->published_at,
            'count' => $this->count,
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
