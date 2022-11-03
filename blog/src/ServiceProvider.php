<?php

namespace Jamstackvietnam\Blog;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @var Router
     */
    protected $router;

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
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->router = $router;

        $this->publishMigrations();
    }

    /**
     * @return void
     */
    protected function publishMigrations()
    {
        if (
            empty(File::glob(database_path('migrations/*_create_posts_table.php'))) &&
            empty(File::glob(database_path('migrations/*_create_post_translations_table.php'))) &&
            empty(File::glob(database_path('migrations/*_create_post_categories_table.php'))) &&
            empty(File::glob(database_path('migrations/*_create_post_category_translations_table.php'))) &&
            empty(File::glob(database_path('migrations/*_create_post_ref_categories_table.php'))) &&
            empty(File::glob(database_path('migrations/*_create_related_posts_table.php')))
        ) {
            $timestamp = date('Y_m_d_Hi', time());
            $post = database_path("migrations/{$timestamp}01_create_posts_table.php");
            $postTranslation = database_path("migrations/{$timestamp}02_create_post_translations_table.php");
            $postCategory = database_path("migrations/{$timestamp}03_create_post_categories_table.php");
            $postCategoryTranslation = database_path("migrations/{$timestamp}04_create_post_category_translations_table.php");
            $postRefCategory = database_path("migrations/{$timestamp}05_create_post_ref_categories_table.php");
            $relatedPost = database_path("migrations/{$timestamp}06_create_related_posts_table.php");

            $this->publishes([
                __DIR__.'/../database/migrations/blog/create_posts_table.php.stub' => $post,
                __DIR__.'/../database/migrations/blog/create_post_translations_table.php.stub' => $postTranslation,
                __DIR__.'/../database/migrations/blog/create_post_categories_table.php.stub' => $postCategory,
                __DIR__.'/../database/migrations/blog/create_post_category_translations_table.php.stub' => $postCategoryTranslation,
                __DIR__.'/../database/migrations/blog/create_post_ref_categories_table.php.stub' => $postRefCategory,
                __DIR__.'/../database/migrations/blog/create_related_posts_table.php.stub' => $relatedPost,
            ], 'migrations');
        }
    }
}
