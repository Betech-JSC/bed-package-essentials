<?php

namespace JamstackVietnam\Job\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;
use \Illuminate\Support\Facades\Route;
use Carbon\Carbon;

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
        'quantity',
        'status',
        'view_count'
    ];

    public $translatedAttributes = [
        'slug',
        'locale',
        'title',
        'description',
        'content',
        'working_position',
        'work_address',
        'working_time',

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

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (request()->route() === null) return;
            $model->published_at = request()->input('published_at') ?? now();
        });

        static::saved(function (self $model) {
            if (request()->route() === null) return;

            if (request()->has('related_jobs')) {
                $model->saveRelateJobs($model);
            }
        });
    }

    public function saveRelateJobs($model)
    {
        $relatedJobs = array_column(request()->input('related_jobs', []), 'id');
        $model->relatedJobs()->sync($relatedJobs, 'id');
    }

    public function relatedJobs()
    {
        return $this->belongsToMany(
            self::class,
            'related_jobs',
            'job_id',
            'related_job_id'
        );
    }

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
            ->where('published_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expected_time')
                    ->orWhereDate('expected_time', '>=', Carbon::today());
            });
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->published_at <= now()
            && ($this->expected_time == null || $this->expected_time >= Carbon::today());
    }

    public function transform()
    {
        return [
            'title' => $this->title,
            'slug' => $this->seo_slug ?? $this->slug,
            'description' => $this->description,
            'working_position' => $this->working_position,
            'work_address' => $this->work_address,
            'working_time' => $this->working_time,
            'expected_time' => $this->expected_time,
            'quantity' => $this->quantity,
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
            'work_address' => $this->work_address,
            'working_time' => $this->working_time,
            'expected_time' => $this->expected_time,
            'published_at' => $this->published_at,
            'quantity' => $this->quantity,
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
