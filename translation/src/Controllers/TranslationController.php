<?php

namespace Jamstackvietnam\Translation\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Amirami\Localizator\Services\Parser;
use Amirami\Localizator\Services\FileFinder;
use Amirami\Localizator\Services\Localizator;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\Translation\Models\Translation;

class TranslationController extends Controller
{
    use HasCrudActions;
    public $model = Translation::class;

    public function beforeIndex($query)
    {
        $this->scanFromView();
        return $query->groupBy('key');
    }

    private function table()
    {
        $items = $this->model::query()
            ->get()
            ->groupBy('key')
            ->map(function ($value, $key) {
                return [
                    'key' => $key,
                    'translations' => $value->keyBy('locale')->map(fn ($translation) => $translation->value)
                ];
            })->values();

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $item = Translation::updateOrCreate(
            $request->only('key', 'locale'),
            $request->only('value'),
        );

        $this->storeToJson();

        return response()->json($item);
    }

    public function scanFromView()
    {
        $localizator = new Localizator;

        $config = config();
        $file = new FileFinder($config);
        $parser = new Parser($config, $file);

        $parser->parseKeys();

        $translations = collect();

        $locale = config('app.locale');
        foreach ($this->getTypes() as $type) {
            $translations = $translations->concat($localizator->collect(
                $parser->getKeys($locale, $type),
                $type,
                $locale,
                true
            ));
        }

        $storedTranslations = Translation::pluck('key');
        $diff = $translations->diff($storedTranslations);

        if ($diff->count()) {
            Translation::insert(
                $diff->transform(fn ($item) => [
                    'key' => $item,
                    'value' => $item,
                    'locale' => $locale
                ])->toArray()
            );
        }
    }

    public function storeToJson()
    {
        $translations = Translation::get()->groupBy('key');
        $appLocales = config('app.locales');
        $defaultLocale = config('app.locale');

        $locales = [];
        foreach ($appLocales as $locale) {
            $locales[$locale] = [];
            foreach ($translations as $key => $translation) {
                $translateLocale = $translation->firstWhere('locale', $locale)?->value;
                $translateDefaultLocale = $translation->firstWhere('locale', $defaultLocale)?->value;

                $locales[$locale][$key] = $translateLocale ?? $translateDefaultLocale;
            }
        }

        foreach ($locales as $locale => $value) {
            $jsonLocales = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

            file_put_contents(public_path("build/locales/$locale.json"), $jsonLocales);
        }
    }

    protected function getLocales(): array
    {
        return $this->argument('lang')
            ? explode(',', $this->argument('lang'))
            : [config('app.locale')];
    }

    protected function getTypes(): array
    {
        return array_keys(array_filter(config('localizator.localize')));
    }
}
