<?php

namespace JamstackVietnam\Tag\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamstackVietnam\Core\Models\BaseModel;
use JamstackVietnam\Core\Traits\Searchable;
use JamstackVietnam\Core\Traits\Translatable;

class Tag extends BaseModel
{
    use HasFactory, Translatable, SoftDeletes, Searchable;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const STATUS_LIST = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_INACTIVE => 'Tắt',
    ];

    public $with = ['translations'];

    public $fillable = [
        'status',
        'position',
        'color',
        'type',
        'icon'
    ];

    public $translatedAttributes = [
        'locale',
        'title',
        'slug',
        'custom_fields',
    ];

    protected $searchable = [
        'columns' => [
            'tag_translations.title' => 10,
            'tag_translations.id' => 5,
        ],
        'joins' => [
            'tag_translations' => ['tag_translations.tag_id', 'tags.id'],
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
            'custom_fields' => $this->custom_fields
        ];
    }

    public function scopeSortByPosition($query)
    {
        return $query->orderByRaw('ISNULL(position) OR position = 0, position ASC')
            ->orderBy('id', 'desc');
    }

    public static function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
