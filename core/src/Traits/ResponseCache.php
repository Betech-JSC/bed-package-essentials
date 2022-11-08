<?php

namespace JamstackVietnam\Core\Traits;

use Illuminate\Support\Facades\Cache;
use \Spiritix\LadaCache\Database\LadaCacheTrait;

trait ResponseCache
{
    use LadaCacheTrait;

    public static function bootResponseCache()
    {
        if (config('cache.default') === 'redis') {
            static::saved(function ($model) {
                Cache::tags($model->cacheKey($model))->flush();
            });

            static::deleted(function ($model) {
                Cache::tags($model->cacheKey($model))->flush();
            });
    }
    }

    private function cacheKey($model)
    {
        return ['cache_response', $model->getTable()];
    }
}
