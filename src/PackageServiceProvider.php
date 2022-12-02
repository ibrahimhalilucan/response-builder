<?php

namespace Chaos\ResponseBuilder;

use Illuminate\Support\ServiceProvider;

/**
 * Class PackageServiceProvider.
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('response_builder', ResponseBuilder::class);
        $this->mergeConfigFrom(__DIR__ . './../config/response-builder.php', 'response-builder');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configurePublishing();
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . './../config/response-builder.php' => config_path('response-builder.php')],
                'response-builder');
        }
    }
}
