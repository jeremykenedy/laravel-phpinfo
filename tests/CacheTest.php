<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use jeremykenedy\LaravelPhpInfo\View\Settings;

class CacheTest extends TestCase
{
    public function test_managed_settings_survive_configuration_caching()
    {
        if (! file_exists($this->app->basePath().'/bootstrap/app.php')) {
            $this->markTestSkipped('This Testbench version has no bootstrap application for config:cache.');
        }

        $path = $this->app->configPath().'/laravelPhpInfoUi.php';
        $this->beforeApplicationDestroyed(function () use ($path) {
            $this->command('config:clear');
            @unlink($path);
        });
        file_put_contents($path, "<?php return ['cssFramework' => 'tailwind', 'view' => 'standalone', 'theme' => 'dark'];");
        list($status, $output) = $this->command('config:cache');

        $this->assertSame(0, $status, $output);
        $cached = require $this->app->getCachedConfigPath();
        $this->app['config']->set('laravelPhpInfoUi', $cached['laravelPhpInfoUi']);
        $settings = new Settings($this->app['config']);
        $this->assertSame('tailwind', $settings->css());
        $this->assertSame('standalone', $settings->view());
        $this->assertSame('dark', $settings->theme());
    }

    public function test_package_routes_survive_serialization()
    {
        $routes = $this->app['router']->getRoutes();
        foreach ($routes as $route) {
            $route->prepareForSerialization();
        }

        $cached = unserialize(serialize($routes));
        $this->containsText('LaravelPhpInfoController@phpinfo', $cached->getByName('laravelPhpInfo::phpinfo')->getActionName());
        $this->containsText('AssetController@show', $cached->getByName('laravelPhpInfo::asset')->getActionName());
    }
}
