<?php

namespace JamstackVietnam\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;
use \Illuminate\Support\Facades\Route;

class PostTag extends BaseModel
{
    use HasFactory, Translatable, SoftDeletes, Searchable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = ['translations'];

    protected $appends = ['url'];

    public $fillable = [
        'status',
        'position',
        'color',
        'icon',
        'view_count'
    ];

    public $translatedAttributes = [
        'locale',
        'title',
        'slug',
        'description',
    ];

    protected $searchable = [
        'columns' => [
            'post_tag_translations.title' => 10,
            'post_tag_translations.id' => 5,
        ],
        'joins' => [
            'post_tag_translations' => ['post_tag_translations.post_tag_id', 'post_tags.id'],
        ],
    ];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'color' => [
                'nullable',
                'regex:/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i',
            ]
        ];
    }


    public function transform()
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'icon' => $this->icon,
            'code' => $this->code,
        ];
    }

    public function transformDetails()
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'icon' => $this->icon,
            'code' => $this->code,
            'description' => $this->description
        ];
    }

    public function scopeSortByPosition($query)
    {
        return $query->orderByRaw('ISNULL(position) OR position = 0, position ASC')
            ->orderBy('id', 'desc');
    }

    public function transformSeo()
    {
        return transform_seo($this);
    }

    public function getUrlAttribute(): array
    {
        $urls = [];
        $default_locale = config('app.locale');

        if ($this->is_active) {
            if (Route::has($default_locale . ".post_tags.show")) {
                foreach ($this->translations as $translation) {
                    $urls[strtoupper($translation->locale)] = route("$translation->locale.post_tags.show", [
                        'slug' => $translation->seo_slug ?? $translation->slug,
                    ]);
                }
            }
        }
        return $urls;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
