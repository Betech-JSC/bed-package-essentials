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

        foreach (config('app.locales') as $locale) {

            foreach ($this->getTypes() as $type) {
                $localizator->collect(
                    $parser->getKeys($locale, $type),
                    $type,
                    $locale,
                    $this->option('remove-missing')
                );
            }
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
