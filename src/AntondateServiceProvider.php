<?php

namespace Ottosmops\Antondate;

use Illuminate\Support\ServiceProvider;
use Ottosmops\Antondate\ValueObjects\AntonDate;

class AntondateServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'antondate');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'ottosmops');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        //$this->mergeConfigFrom(__DIR__.'/../config/antondate.php', 'antondate');

        // Register the service the package provides.
        //$this->app->singleton('antondate', function ($app) {
        //    return new AntonDate;
        //});
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides() : array
    {
        return ['antondate'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        //$this->publishes([
        //    __DIR__.'/../config/antondate.php' => config_path('antondate.php'),
        //], 'antondate.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/ottosmops'),
        ], 'antondate.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ottosmops'),
        ], 'antondate.views');*/

        // Publishing the translation files.
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ottosmops'),
        ],);

        // Registering package commands.
        // $this->commands([]);
    }
}
