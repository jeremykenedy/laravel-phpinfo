<?php

namespace jeremykenedy\LaravelPhpInfo;

use Illuminate\Support\ServiceProvider;

class LaravelPhpInfoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/laravelPhpInfo.php', 'laravelPhpInfo');
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravelPhpInfo');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'laravelPhpInfo');
        $this->app['view']->composer('laravelPhpInfo::phpinfo.php-info', 'jeremykenedy\\LaravelPhpInfo\\View\\PhpInfoComposer');

        if ($this->app->runningInConsole()) {
            $this->publishFiles();
            $this->commands([
                'jeremykenedy\\LaravelPhpInfo\\Console\\InstallCommand',
                'jeremykenedy\\LaravelPhpInfo\\Console\\UpdateCommand',
                'jeremykenedy\\LaravelPhpInfo\\Console\\SwitchCommand',
            ]);
        }
    }

    private function publishFiles()
    {
        $languagePath = method_exists($this->app, 'langPath')
            ? $this->app->langPath()
            : $this->app->resourcePath().'/lang';

        $paths = [
            'config' => [__DIR__.'/config/laravelPhpInfo.php' => $this->app->configPath().'/laravelPhpInfo.php'],
            'views' => [__DIR__.'/resources/views' => $this->app->resourcePath().'/views/vendor/laravelPhpInfo'],
            'lang' => [__DIR__.'/resources/lang' => $languagePath.'/vendor/laravelPhpInfo'],
        ];

        foreach ($paths as $tag => $files) {
            $this->publishes($files, 'laravelPhpInfo');
            $this->publishes($files, 'laravelPhpInfo-'.$tag);
        }
    }
}
