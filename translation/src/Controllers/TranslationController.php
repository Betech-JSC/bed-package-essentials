<?php

namespace Jamstackvietnam\Translation\Controllers;

use App\Http\Controllers\Controller;
use Jamstackvietnam\Core\Traits\HasCrudActions;
use Jamstackvietnam\Translation\Models\Translation;
use Amirami\Localizator\Services\Localizator;
use Amirami\Localizator\Services\Parser;
use Amirami\Localizator\Services\FileFinder;

class TranslationController extends Controller
{
    use HasCrudActions;
    public $model = Translation::class;

    public function beforeIndex($query)
    {
        $this->generate();
        dd('1');
        // return $query->groupBy('key');
    }

    public function generate()
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
