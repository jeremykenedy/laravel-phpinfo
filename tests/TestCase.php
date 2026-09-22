<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use jeremykenedy\LaravelPhpInfo\Setup;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return ['jeremykenedy\\LaravelPhpInfo\\LaravelPhpInfoServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('view.paths', [__DIR__.'/fixtures/views']);
        $app['config']->set('session.driver', 'array');
    }

    protected function command($name, array $options = [])
    {
        $kernel = $this->app->make(Kernel::class);
        $status = $kernel->call($name, array_merge(['--no-interaction' => true], $options));

        return [$status, $kernel->output()];
    }

    protected function prepareInstallation()
    {
        $path = sys_get_temp_dir().'/laravel-phpinfo-test-'.uniqid();
        $files = new Filesystem;
        $files->makeDirectory($path.'/config', 0755, true);
        $files->makeDirectory($path.'/resources/views/vendor', 0755, true);
        $this->app->instance(Setup::class, new Setup(new Application($path), $files));
        Application::setInstance($this->app);
        $this->beforeApplicationDestroyed(function () use ($files, $path) {
            $files->deleteDirectory($path);
        });

        return $path;
    }

    protected function containsText($needle, $haystack)
    {
        $this->assertTrue(strpos($haystack, $needle) !== false, 'Expected output to contain: '.$needle);
    }

    protected function excludesText($needle, $haystack)
    {
        $this->assertFalse(strpos($haystack, $needle) !== false, 'Expected output not to contain: '.$needle);
    }
}
