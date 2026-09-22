<?php

namespace jeremykenedy\LaravelPhpInfo\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use jeremykenedy\LaravelPhpInfo\Setup;
use jeremykenedy\LaravelPhpInfo\View\Settings;

abstract class SetupCommand extends Command
{
    protected $operation;

    private $setup;

    private $settings;

    public function __construct(Setup $setup, Settings $settings)
    {
        parent::__construct();
        $this->setup = $setup;
        $this->settings = $settings;
    }

    public function handle()
    {
        if ($this->operation === 'install' && $this->setup->installed() && ! $this->option('force')) {
            $this->info('Laravel PHP Info is already installed. Use phpinfo:update to change settings, or --force to run setup again.');

            return 0;
        }

        try {
            $options = $this->selections();
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        if ($this->option('ui-kit') && $this->configureUiKit($options['cssFramework']) !== 0) {
            return 1;
        }

        $backup = $this->setup->save($options, $this->option('refresh-views'));
        $this->call('config:clear');
        $this->call('view:clear');
        $this->info('Laravel PHP Info configured: '.$options['cssFramework'].', '.$options['view'].', '.$options['theme'].'.');

        if ($backup) {
            $this->info('Previous views saved to '.$backup);
        } elseif ($this->setup->hasPublishedViews()) {
            $this->warn('Published views were kept. Use --refresh-views to back them up and install the current templates.');
        }

        $this->line('Rebuild cached configuration if your deployment uses it. Run npm run build if your application bundles framework CSS.');

        return 0;
    }

    private function selections()
    {
        $choices = [
            'css' => ['bootstrap3', 'bootstrap4', 'bootstrap5', 'tailwind'],
            'view' => ['app', 'standalone'],
            'theme' => ['system', 'light', 'dark'],
        ];
        $current = ['css' => $this->settings->css(), 'view' => $this->settings->view(), 'theme' => $this->settings->theme()];
        $selected = [];

        foreach ($choices as $key => $values) {
            $value = $this->option($key);

            if ($value !== null && ! in_array($value, $values, true)) {
                throw new InvalidArgumentException('Invalid --'.$key.'. Choose '.implode(', ', $values).'.');
            }

            $selected[$key] = $value;
        }

        if ($this->option('frontend') !== null && $this->option('frontend') !== 'blade') {
            throw new InvalidArgumentException('PHP Info renders a Blade page. The supported --frontend value is blade.');
        }

        $hasSelection = count(array_filter($selected, function ($value) {
            return $value !== null;
        })) > 0;

        if ($this->operation === 'switch' && ! $hasSelection) {
            throw new InvalidArgumentException('Pass --css, --view, or --theme to phpinfo:switch.');
        }

        foreach ($choices as $key => $values) {
            if (! $hasSelection && $this->input->isInteractive() && $this->operation !== 'switch') {
                $selected[$key] = $this->choice(ucfirst($key), $values, array_search($current[$key], $values, true));
            }

            if ($selected[$key] === null) {
                $selected[$key] = $current[$key];
            }
        }

        return ['cssFramework' => $selected['css'], 'view' => $selected['view'], 'theme' => $selected['theme']];
    }

    private function configureUiKit($css)
    {
        if ($css === 'bootstrap3') {
            $this->error('Laravel UI Kit supports Bootstrap 4, Bootstrap 5, and Tailwind. Bootstrap 3 remains available without --ui-kit.');

            return 1;
        }

        $command = $this->setup->uiKitInstalled() ? 'ui-kit:update' : 'ui-kit:install';

        if (! $this->getApplication()->has($command)) {
            $this->error('Install jeremykenedy/laravel-ui-kit with Composer before using --ui-kit.');

            return 1;
        }

        return $this->call($command, ['--css' => $css, '--frontend' => 'blade', '--no-interaction' => true]);
    }
}
