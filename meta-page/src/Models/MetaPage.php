<?php

namespace Jamstackvietnam\MetaPage\Models;

use Jamstackvietnam\Sitemap\Sitemap;
use Illuminate\Database\Eloquent\Model;

class MetaPage extends Model
{
    public $fillable = [
        'url',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',

        'inject_head',
        'inject_body_start',
        'inject_body_end'
    ];

    public $rules = [
        'seo_meta_title' => 'required',
        'seo_meta_description' => 'required',
    ];
}
