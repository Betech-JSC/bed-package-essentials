<?php

namespace Jamstackvietnam\Redirect\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jamstackvietnam\Redirect\Contracts\RedirectModelContract;
use Jamstackvietnam\Redirect\Exceptions\RedirectException;
use Nicolaslopezj\Searchable\SearchableTrait;

class Redirect extends Model implements RedirectModelContract
{
    use SearchableTrait;

    public $fillable = [
        'old_url',
        'new_url',
        'status_code',
        'is_active'
    ];

    protected $searchable = [
        'columns' => [
            'redirects.old_url' => 5,
            'redirects.new_url' => 5,
            'redirects.status_code' => 5,
        ],
    ];

    public $rules = [
        'old_url' => 'required',
        'new_url' => 'required',
        'status_code' => 'required',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (self $model) {
            if (trim(strtolower($model->old_url), '/') == trim(strtolower($model->new_url), '/')) {
                throw RedirectException::sameUrls();
            }

            static::whereOldUrl($model->new_url)->whereNewUrl($model->old_url)->delete();

            $model->syncOldRedirects($model, $model->new_url);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter the query by an old url.
     *
     * @param Builder $query
     * @param string $url
     *
     * @return mixed
     */
    public function scopeWhereOldUrl($query, string $url)
    {
        return $query->where('old_url', $url);
    }

    /**
     * Filter the query by a new url.
     *
     * @param Builder $query
     * @param string $url
     *
     * @return mixed
     */
    public function scopeWhereNewUrl($query, string $url)
    {
        return $query->where('new_url', $url);
    }

    /**
     * Get all redirect statuses defined inside the "config/redirects.php" file.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return (array) config('redirects.statuses', []);
    }

    /**
     * Sync old redirects to point to the new (final) url.
     *
     * @param RedirectModelContract $model
     * @param string $finalUrl
     * @return void
     */
    public function syncOldRedirects(RedirectModelContract $model, string $finalUrl): void
    {
        $items = static::whereNewUrl($model->old_url)->get();

        foreach ($items as $item) {
            $item->update(['new_url' => $finalUrl]);
            $item->syncOldRedirects($model, $finalUrl);
        }
    }

    /**
     * Return a valid redirect entity for a given path (old url).
     * A redirect is valid if:
     * - it has an url to redirect to (new url)
     * - it's status code is one of the statuses defined on this model.
     *
     * @param string $path
     * @return Redirect|null
     */
    public static function findValidOrNull($path): ?RedirectModelContract
    {
        return static::where('old_url', $path === '/' ? $path : trim($path, '/'))
            ->whereNotNull('new_url')
            ->whereIn('status_code', array_keys(self::getStatuses()))
            ->latest()->first();
    }
}
