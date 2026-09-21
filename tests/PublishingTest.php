<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use Illuminate\Support\ServiceProvider;
use jeremykenedy\LaravelPhpInfo\LaravelPhpInfoServiceProvider;

class PublishingTest extends TestCase
{
    public function test_translations_publish_to_the_application_language_path()
    {
        $paths = ServiceProvider::pathsToPublish(LaravelPhpInfoServiceProvider::class, 'laravelPhpInfo-lang');

        $this->assertSame([$this->app->langPath().'/vendor/laravelPhpInfo'], array_values($paths));
    }

    public function test_custom_language_path_is_respected()
    {
        if (! method_exists($this->app, 'useLangPath')) {
            $this->markTestSkipped('Custom language paths were added in newer Laravel releases.');
        }

        $path = sys_get_temp_dir().'/phpinfo-custom-lang';
        $this->app->useLangPath($path);
        $provider = new LaravelPhpInfoServiceProvider($this->app);
        $provider->boot();
        $paths = ServiceProvider::pathsToPublish(LaravelPhpInfoServiceProvider::class, 'laravelPhpInfo-lang');

        $this->assertSame([$path.'/vendor/laravelPhpInfo'], array_values($paths));
    }

    public function test_publishing_translations_preserves_existing_translations()
    {
        $destination = $this->app->langPath().'/vendor/laravelPhpInfo';
        $files = $this->app['files'];
        $this->beforeApplicationDestroyed(function () use ($files, $destination) {
            $files->deleteDirectory($destination);
        });
        $files->makeDirectory($destination.'/en', 0755, true, true);
        $path = $destination.'/en/laravel-phpinfo.php';
        $custom = "<?php return ['title' => 'Custom title'];";
        $files->put($path, $custom);
        list($status) = $this->command('vendor:publish', ['--tag' => 'laravelPhpInfo-lang']);

        $this->assertSame(0, $status);
        $this->assertSame($custom, $files->get($path));
        $files->delete($path);
        list($status) = $this->command('vendor:publish', ['--tag' => 'laravelPhpInfo-lang']);
        $this->assertSame(0, $status);
        $this->assertFileExists($path);
        $translations = require $path;
        $this->assertSame('PHP Information', $translations['title']);
    }

    public function test_original_publish_tag_still_includes_all_three_resources()
    {
        $paths = ServiceProvider::pathsToPublish(LaravelPhpInfoServiceProvider::class, 'laravelPhpInfo');

        $this->assertCount(3, $paths);
        $this->assertTrue(in_array($this->app->configPath().'/laravelPhpInfo.php', $paths, true));
        $this->assertTrue(in_array($this->app->resourcePath().'/views/vendor/laravelPhpInfo', $paths, true));
        $this->assertTrue(in_array($this->app->langPath().'/vendor/laravelPhpInfo', $paths, true));
    }

    public function test_published_view_overrides_the_package_view()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $this->app['view']->prependNamespace('laravelPhpInfo', __DIR__.'/fixtures/published');
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(200, $response->getStatusCode());
        $this->containsText('Published PHP Info view', $response->getContent());
    }
}
