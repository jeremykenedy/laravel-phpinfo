<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Route;
use jeremykenedy\LaravelPhpInfo\PhpInfo;
use Mockery;

class FeatureTest extends TestCase
{
    public function test_guest_cannot_read_php_info_by_default()
    {
        $response = $this->call('GET', '/phpinfo', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertSame(401, $response->getStatusCode());
        $this->excludesText('PHP Version', $response->getContent());
    }

    public function test_authenticated_user_can_read_php_info()
    {
        $this->app['auth']->guard()->setUser(new GenericUser(['id' => 1, 'email' => 'test@example.com']));
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(200, $response->getStatusCode());
        $this->containsText('PHP Information', $response->getContent());
        $this->containsText('data-host-layout', $response->getContent());
        $this->containsText('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_auth_can_still_be_disabled_explicitly()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(200, $response->getStatusCode());
        $this->containsText('phpinfo-card card', $response->getContent());
        $this->assertSame('http://localhost/phpinfo', route('laravelPhpInfo::phpinfo'));
    }

    public function test_configured_roles_middleware_is_enforced()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $this->app['config']->set('laravelPhpInfo.rolesEnabled', true);
        $this->app['config']->set('laravelPhpInfo.rolesMiddlware', RejectAccess::class);
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_existing_layout_and_card_customization_still_work()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $this->app['config']->set('laravelPhpInfo.laravelPhpInfoBladeExtended', 'custom');
        $this->app['config']->set('laravelPhpInfo.bootstrapCardClasses', 'host-card');
        $this->app['config']->set('laravelPhpInfo.bootstapVersion', '3');
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(200, $response->getStatusCode());
        $this->containsText('data-custom-layout', $response->getContent());
        $this->containsText('panel panel-default host-card', $response->getContent());
    }

    public function test_standalone_layout_does_not_require_a_host_view()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $this->app['config']->set('laravelPhpInfoUi.view', 'standalone');
        $this->app['config']->set('laravelPhpInfo.laravelPhpInfoBladeExtended', 'does-not-exist');
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(200, $response->getStatusCode());
        $this->containsText('<!DOCTYPE html>', $response->getContent());
        $this->excludesText('data-host-layout', $response->getContent());
    }

    public function test_plain_php_info_output_is_escaped()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $info = Mockery::mock(PhpInfo::class);
        $info->shouldReceive('capture')->once()->andReturn(['output' => '<script>alert(1)</script>', 'html' => false, 'available' => true]);
        $this->app->instance(PhpInfo::class, $info);
        $response = $this->call('GET', '/phpinfo');

        $this->containsText('&lt;script&gt;', $response->getContent());
        $this->excludesText('<script>alert', $response->getContent());
    }

    public function test_disabled_php_info_has_an_explanation()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $info = Mockery::mock(PhpInfo::class);
        $info->shouldReceive('capture')->once()->andReturn(['output' => '', 'html' => false, 'available' => false]);
        $this->app->instance(PhpInfo::class, $info);
        $response = $this->call('GET', '/phpinfo');

        $this->assertSame(200, $response->getStatusCode());
        $this->containsText('disabled on this server', $response->getContent());
    }

    public function test_styles_can_still_be_disabled()
    {
        $this->app['config']->set('laravelPhpInfo.authEnabled', false);
        $this->app['config']->set('laravelPhpInfo.usePHPinfoCSS', false);
        $response = $this->call('GET', '/phpinfo');

        $this->excludesText('rel="stylesheet"', $response->getContent());
    }

    public function test_static_assets_are_available_without_exposing_php_info()
    {
        foreach (['php-info.css' => 'text/css', 'php-info.js' => 'application/javascript'] as $file => $type) {
            $response = $this->call('GET', '/phpinfo/assets/'.$file);
            $this->assertSame(200, $response->getStatusCode());
            $this->containsText($type, $response->headers->get('Content-Type'));
            $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
            $this->excludesText('PHP Version', $response->getContent());
        }
    }

    public function test_asset_route_rejects_other_files()
    {
        foreach (['laravelPhpInfo.php', 'php-info.css.php', '..%2Fconfig%2FlaravelPhpInfo.php'] as $file) {
            $response = $this->call('GET', '/phpinfo/assets/'.$file);
            $this->assertSame(404, $response->getStatusCode());
        }
    }

    public function test_routes_can_be_prepared_for_caching()
    {
        $route = Route::getRoutes()->getByName('laravelPhpInfo::phpinfo');
        $route->prepareForSerialization();

        $this->containsText('LaravelPhpInfoController@phpinfo', $route->getActionName());
    }
}
