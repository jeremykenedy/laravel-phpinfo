<?php

namespace jeremykenedy\LaravelPhpInfo;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelPhpInfoServiceProvider extends ServiceProvider
{
    private $_packageTag = 'laravelPhpInfo';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang/', $this->_packageTag);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views/', $this->_packageTag);
        $this->mergeConfigFrom(__DIR__ . '/config/' . $this->_packageTag . '.php', $this->_packageTag);
        $this->publishFiles();
    }

    /**
     * Publish files for Laravel PHP Info.
     *
     * @return void
     */
    private function publishFiles()
    {
        $publishTag = $this->_packageTag;

        $this->publishes([
            __DIR__ . '/config/' . $this->_packageTag . '.php' => base_path('config/' . $this->_packageTag . '.php'),
        ], $publishTag);

        $this->publishes([
            __DIR__ . '/resources/views' => base_path('resources/views/vendor/' . $this->_packageTag),
        ], $publishTag);

        $this->publishes([
            __DIR__ . '/resources/lang' => base_path('resources/lang/vendor/' . $this->_packageTag),
        ], $publishTag);
    }
}
