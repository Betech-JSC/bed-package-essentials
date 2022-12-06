<?php

namespace JamstackVietnam\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $rules = [
        'group' => 'nullable',
        'name' => 'nullable',
    ];

    public static function rules($id)
    {
        return [
            'general' => [
                'general_business_name' => 'required',
                'general_site_name' => 'nullable',
                'general_email' => 'nullable',

                'seo_title_separator' => 'nullable',
                'seo_meta_title' => 'nullable|max:170',
                'seo_meta_description' => 'nullable|max:255',
                'seo_meta_keywords' => 'nullable',
                'seo_meta_robots' => 'nullable',
                'seo_canonical' => 'nullable',
                'seo_image' => 'nullable',
                'seo_schemas' => 'nullable',

                'inject_head' => 'nullable',
                'inject_body_start' => 'nullable',
                'inject_body_end' => 'nullable',
            ],
            'smtp' => [
                'mail_from_address' => 'required|email',
                'mail_from_name' => 'required',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_host' => 'required',
                'mail_port' => 'required',
                'mail_encryption' => 'required',
            ],
            'notification' => [
                'notification_production_to' => 'required',
                'notification_staging_to' => 'required',
            ],
            'robots_txt' => [
                'robots_txt'  => 'required',
            ]
        ][$id] ?? [];
    }
}
