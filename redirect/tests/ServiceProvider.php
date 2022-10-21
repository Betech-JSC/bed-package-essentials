<?php

namespace Jamstackvietnam\Redirect\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Jamstackvietnam\Redirect\Contracts\RedirectModelContract;
use Jamstackvietnam\Redirect\Models\Redirect;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Create a new service provider instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->publishConfigs();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    /**
     * Setup the config.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function publishConfigs()
    {
        $this->app->make('config')->set('redirects.statuses', [
            302 => 'Normal (302)',
            301 => 'Permanent (301)',
            307 => 'Temporary (307)',
        ]);
    }

    /**
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind(RedirectModelContract::class, $this->config['redirects']['redirect_model'] ?? Redirect::class);
        $this->app->alias(RedirectModelContract::class, 'redirect.model');
    }
}
