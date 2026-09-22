<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use jeremykenedy\LaravelPhpInfo\View\Settings;

class SettingsTest extends TestCase
{
    public function test_existing_defaults_remain_unchanged()
    {
        $config = $this->app['config'];
        $settings = new Settings($config);

        $this->assertSame('4', $config->get('laravelPhpInfo.bootstapVersion'));
        $this->assertSame('bootstrap4', $settings->css());
        $this->assertSame('app', $settings->view());
        $this->assertSame('layouts.app', $config->get('laravelPhpInfo.laravelPhpInfoBladeExtended'));
        $this->assertTrue($config->get('laravelPhpInfo.authEnabled'));
        $this->assertFalse($config->get('laravelPhpInfo.rolesEnabled'));
        $this->assertSame('role:admin', $config->get('laravelPhpInfo.rolesMiddlware'));
    }

    public function test_legacy_bootstrap_setting_still_controls_the_framework()
    {
        $settings = new Settings($this->app['config']);
        $this->app['config']->set('laravelPhpInfo.bootstapVersion', '3');
        $this->assertSame('bootstrap3', $settings->css());
        $this->assertSame('panel panel-default', $settings->classes()['card']);
    }

    public function test_each_framework_has_its_own_wrapper_classes()
    {
        $settings = new Settings($this->app['config']);
        foreach (['bootstrap3' => 'panel panel-default', 'bootstrap4' => 'card', 'bootstrap5' => 'card', 'tailwind' => 'overflow-hidden rounded-2xl border border-slate-200 shadow-sm'] as $css => $classes) {
            $this->app['config']->set('laravelPhpInfo.cssFramework', $css);
            $this->assertSame($css, $settings->css());
            $this->assertSame($classes, $settings->classes()['card']);
        }
    }

    public function test_managed_selections_override_only_display_settings()
    {
        $this->app['config']->set('laravelPhpInfoUi', ['cssFramework' => 'tailwind', 'view' => 'standalone', 'theme' => 'dark']);
        $settings = new Settings($this->app['config']);

        $this->assertSame('tailwind', $settings->css());
        $this->assertSame('standalone', $settings->view());
        $this->assertSame('dark', $settings->theme());
        $this->assertTrue($this->app['config']->get('laravelPhpInfo.authEnabled'));
    }

    public function test_invalid_display_settings_fall_back_safely()
    {
        $this->app['config']->set('laravelPhpInfoUi', ['cssFramework' => 'missing', 'view' => '../missing', 'theme' => 'missing']);
        $settings = new Settings($this->app['config']);

        $this->assertSame('bootstrap4', $settings->css());
        $this->assertSame('app', $settings->view());
        $this->assertSame('system', $settings->theme());
    }
}
