<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use jeremykenedy\LaravelPhpInfo\Setup;
use Mockery;
use RuntimeException;

class CommandTest extends TestCase
{
    public function test_install_keeps_the_existing_defaults()
    {
        $path = $this->prepareInstallation();
        list($status) = $this->command('phpinfo:install');
        $this->assertSame(0, $status);
        $this->assertFileExists($path.'/config/laravelPhpInfo.php');
        $this->assertSame(['cssFramework' => 'bootstrap4', 'view' => 'app', 'theme' => 'system'], require $path.'/config/laravelPhpInfoUi.php');
        $this->assertFalse(is_dir($path.'/resources/views/vendor/laravelPhpInfo'));
    }

    public function test_install_detects_an_existing_installation()
    {
        $path = $this->prepareInstallation();
        $custom = "<?php\nreturn ['authEnabled' => false, 'custom' => 'keep me'];\n";
        file_put_contents($path.'/config/laravelPhpInfo.php', $custom);
        list($status, $output) = $this->command('phpinfo:install', ['--css' => 'tailwind']);

        $this->assertSame(0, $status);
        $this->containsText('already installed', $output);
        $this->assertSame($custom, file_get_contents($path.'/config/laravelPhpInfo.php'));
        $this->assertFalse(file_exists($path.'/config/laravelPhpInfoUi.php'));
    }

    public function test_forced_install_still_preserves_custom_configuration()
    {
        $path = $this->prepareInstallation();
        $custom = "<?php\nreturn ['rolesEnabled' => true, 'rolesMiddlware' => 'admin'];\n";
        file_put_contents($path.'/config/laravelPhpInfo.php', $custom);
        list($status) = $this->command('phpinfo:install', ['--force' => true, '--css' => 'bootstrap5', '--view' => 'standalone', '--theme' => 'dark']);

        $this->assertSame(0, $status);
        $this->assertSame($custom, file_get_contents($path.'/config/laravelPhpInfo.php'));
        $this->assertSame(['cssFramework' => 'bootstrap5', 'view' => 'standalone', 'theme' => 'dark'], require $path.'/config/laravelPhpInfoUi.php');
    }

    public function test_update_preserves_configuration_and_published_views()
    {
        $path = $this->prepareInstallation();
        $custom = "<?php\nreturn ['custom' => 'keep me'];\n";
        file_put_contents($path.'/config/laravelPhpInfo.php', $custom);
        mkdir($path.'/resources/views/vendor/laravelPhpInfo/phpinfo', 0755, true);
        $view = $path.'/resources/views/vendor/laravelPhpInfo/phpinfo/php-info.blade.php';
        file_put_contents($view, 'My custom view');
        list($status, $output) = $this->command('phpinfo:update', ['--css' => 'tailwind']);

        $this->assertSame(0, $status);
        $this->assertSame($custom, file_get_contents($path.'/config/laravelPhpInfo.php'));
        $this->assertSame('My custom view', file_get_contents($view));
        $this->containsText('Published views were kept', $output);
    }

    public function test_switch_only_changes_the_requested_setting()
    {
        $path = $this->prepareInstallation();
        $this->app['config']->set('laravelPhpInfoUi', ['cssFramework' => 'bootstrap3', 'view' => 'standalone', 'theme' => 'dark']);
        list($status) = $this->command('phpinfo:switch', ['--css' => 'tailwind']);

        $this->assertSame(0, $status);
        $this->assertSame(['cssFramework' => 'tailwind', 'view' => 'standalone', 'theme' => 'dark'], require $path.'/config/laravelPhpInfoUi.php');
    }

    public function test_invalid_options_do_not_write_files()
    {
        $path = $this->prepareInstallation();
        foreach (['--css' => 'invalid', '--view' => '../../file', '--theme' => 'invalid', '--frontend' => 'vue'] as $option => $value) {
            list($status) = $this->command('phpinfo:install', [$option => $value]);
            $this->assertSame(1, $status);
            $this->assertFalse(file_exists($path.'/config/laravelPhpInfo.php'));
            $this->assertFalse(file_exists($path.'/config/laravelPhpInfoUi.php'));
        }
    }

    public function test_switch_requires_a_selection()
    {
        $path = $this->prepareInstallation();
        list($status) = $this->command('phpinfo:switch');

        $this->assertSame(1, $status);
        $this->assertFalse(file_exists($path.'/config/laravelPhpInfoUi.php'));
    }

    public function test_refresh_views_backs_up_existing_templates()
    {
        $path = $this->prepareInstallation();
        mkdir($path.'/resources/views/vendor/laravelPhpInfo/phpinfo', 0755, true);
        $view = $path.'/resources/views/vendor/laravelPhpInfo/phpinfo/php-info.blade.php';
        file_put_contents($view, 'My custom view');
        list($status) = $this->command('phpinfo:update', ['--refresh-views' => true, '--css' => 'bootstrap5']);
        $backups = glob($path.'/resources/views/vendor/laravelPhpInfo.backup-*');

        $this->assertSame(0, $status);
        $this->assertCount(1, $backups);
        $this->assertSame('My custom view', file_get_contents($backups[0].'/phpinfo/php-info.blade.php'));
        $this->assertSame(file_get_contents(__DIR__.'/../src/resources/views/phpinfo/php-info.blade.php'), file_get_contents($view));
    }

    public function test_failed_backup_does_not_overwrite_templates()
    {
        $path = $this->prepareInstallation();
        $files = Mockery::mock(Filesystem::class)->makePartial();
        $files->shouldReceive('copyDirectory')->once()->andReturn(false);
        mkdir($path.'/resources/views/vendor/laravelPhpInfo/phpinfo', 0755, true);
        $view = $path.'/resources/views/vendor/laravelPhpInfo/phpinfo/php-info.blade.php';
        file_put_contents($view, 'My custom view');
        $setup = new Setup(new Application($path), $files);
        Application::setInstance($this->app);

        try {
            $setup->save(['cssFramework' => 'bootstrap5'], true);
            $this->fail('A failed backup must stop the refresh.');
        } catch (RuntimeException $exception) {
            $this->containsText('Could not back up', $exception->getMessage());
        }

        $this->assertSame('My custom view', file_get_contents($view));
        $this->assertFalse(file_exists($path.'/config/laravelPhpInfoUi.php'));
    }

    public function test_failed_settings_write_preserves_previous_selections()
    {
        $path = $this->prepareInstallation();
        $previous = "<?php return ['cssFramework' => 'bootstrap3'];";
        file_put_contents($path.'/config/laravelPhpInfoUi.php', $previous);
        $files = Mockery::mock(Filesystem::class)->makePartial();
        $files->shouldReceive('put')->once()->andReturn(false);
        $setup = new Setup(new Application($path), $files);
        Application::setInstance($this->app);

        try {
            $setup->save(['cssFramework' => 'tailwind']);
            $this->fail('A failed settings write must stop setup.');
        } catch (RuntimeException $exception) {
            $this->containsText('Could not save', $exception->getMessage());
        }

        $this->assertSame($previous, file_get_contents($path.'/config/laravelPhpInfoUi.php'));
        $this->assertSame([], glob($path.'/config/phpinfo-*'));
    }

    public function test_ui_kit_is_optional_and_must_be_installed_explicitly()
    {
        $path = $this->prepareInstallation();
        list($status, $output) = $this->command('phpinfo:install', ['--ui-kit' => true, '--css' => 'tailwind']);

        $this->assertSame(1, $status);
        $this->containsText('Install jeremykenedy/laravel-ui-kit', $output);
        $this->assertFalse(file_exists($path.'/config/laravelPhpInfo.php'));
    }

    public function test_ui_kit_rejects_bootstrap_three_before_writing_anything()
    {
        $path = $this->prepareInstallation();
        list($status) = $this->command('phpinfo:install', ['--ui-kit' => true, '--css' => 'bootstrap3']);

        $this->assertSame(1, $status);
        $this->assertFalse(file_exists($path.'/config/laravelPhpInfo.php'));
    }

    public function test_ui_kit_receives_the_selected_framework_only_when_requested()
    {
        $this->prepareInstallation();
        $stub = new UiKitCommand;
        $this->app->make(Kernel::class)->registerCommand($stub);
        list($status) = $this->command('phpinfo:install', ['--ui-kit' => true, '--css' => 'bootstrap5']);

        $this->assertSame(0, $status);
        $this->assertSame('bootstrap5', $stub->receivedCss);
        $this->assertSame('blade', $stub->receivedFrontend);
    }

    public function test_ui_kit_failure_stops_package_setup()
    {
        $path = $this->prepareInstallation();
        $stub = new UiKitCommand;
        $stub->status = 1;
        $this->app->make(Kernel::class)->registerCommand($stub);
        list($status) = $this->command('phpinfo:install', ['--ui-kit' => true, '--css' => 'tailwind']);

        $this->assertSame(1, $status);
        $this->assertFalse(file_exists($path.'/config/laravelPhpInfo.php'));
    }

    public function test_an_existing_ui_kit_installation_uses_its_safe_update_command()
    {
        $path = $this->prepareInstallation();
        file_put_contents($path.'/config/ui-kit.php', '<?php return [];');
        $stub = new UiKitCommand;
        $stub->setName('ui-kit:update');
        $this->app->make(Kernel::class)->registerCommand($stub);
        list($status) = $this->command('phpinfo:update', ['--ui-kit' => true, '--css' => 'tailwind']);

        $this->assertSame(0, $status);
        $this->assertSame('tailwind', $stub->receivedCss);
    }
}
