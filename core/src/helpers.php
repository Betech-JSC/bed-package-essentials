<?php

if (!function_exists('package_path')) {
    function package_path($path)
    {
        return base_path('packages/jam-package-essentials/' . $path);
    }
}

if (!function_exists('static_url')) {
    function static_url($path, $parameters = [], $absolute = true)
    {
        if (!$path || str_contains($path, 'http')) return $path;
        if (!empty($parameters)) {
            $url = config('app.static_url') . '/' . $path . '?' . http_build_query($parameters);
        } else {
            $url = config('app.static_url') . '/' . $path;
        }
        if (!$absolute) {
            $url = collect(parse_url($url))->only('path', 'query')->join(',');
        }

        return $url;
    }
}

if (!function_exists('transform_seo')) {
    function transform_seo($model)
    {
        return [
            'seo_meta_title' => $model->seo_meta_title ?? $model->title,
            'seo_slug' => $model->seo_slug ?? $model->slug,
            'seo_meta_description' => $model->seo_meta_description ?? $model->description,
            'seo_meta_keywords' => $model->seo_meta_keywords,
            'seo_meta_robots' => $model->seo_meta_robots,
            'seo_canonical' => $model->seo_canonical,
            'seo_image' => static_url($model->seo_image ?? $model->image_url),
            'seo_schemas' => $model->seo_schemas,
        ];
    }
}

if (!function_exists('generate_code')) {
    function generate_code($length = 15, $characters = 'all'): string
    {
        $characters = [
            'all' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'number' => '0123456789',
            'uppercase' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
        ][$characters];

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('cache_response')) {
    function cache_response($key, $handle, ...$tags)
    {
        array_unshift($tags, 'cache_response');
        if (env('LADA_CACHE_ACTIVE', false)) {
            return Illuminate\Support\Facades\Cache::tags($tags)
                ->rememberForever($key, $handle);
        } else {
            return $handle();
        }
    }
}

if (!function_exists('current_admin_id')) {
    function current_admin_id()
    {
        return auth()->guard('admin')->user()->id;
    }
}

if (!function_exists('current_admin')) {
    function current_admin()
    {
        return auth()->guard('admin')->user();
    }
}

if (!function_exists('to_number')) {
    function to_number($number)
    {
        $number = is_numeric($number) ? $number : 0;
        return number_format($number, 0, '.', '.');
    }
}

if (!function_exists('to_money')) {
    function to_money($number)
    {
        return to_number($number) . ' ATN';
    }
}

if (!function_exists('to_date')) {
    function to_date($date, $format = 'd/m/Y H:i')
    {
        if (empty($date)) return '';

        return ucfirst(Illuminate\Support\Carbon::create($date)
            ->setTimezone(config('app.timezone'))
            ->translatedFormat($format));
    }
}

if (!function_exists('is_localhost')) {
    function is_localhost(): bool
    {
        return in_array(request()->getHost(), ['localhost', '127.0.0.1']);
    }
}

if (!function_exists('current_locale')) {
    function current_locale()
    {
        $default = config('localized-routes.omit_url_prefix_for_locale');

        if (request()->route() === null) return $default;

        $prefix = request()->route()->getPrefix();
        $segments = explode('/', $prefix);

        $lang = last($segments) ? last($segments) : $default;

        if (head($segments) === 'admin') {
            if (count($segments) === 1) {
                $lang = $default;
            }
        }

        if (!in_array($lang, config('app.locales'))) {
            return config('app.locale');
        }

        return $lang;
    }
}

if (!function_exists('notification_to')) {
    function notification_to()
    {
        if(!is_localhost()) {
            return settings()->group('notification')->get('notification_production_to', null);
        }
        else {
            return settings()->group('notification')->get('notification_staging_to', null);
        }
    }
}
