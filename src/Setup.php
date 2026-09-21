<?php

namespace jeremykenedy\LaravelPhpInfo;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class Setup
{
    private $app;

    private $files;

    public function __construct(Application $app, Filesystem $files)
    {
        $this->app = $app;
        $this->files = $files;
    }

    public function installed()
    {
        return $this->files->exists($this->app->configPath().'/laravelPhpInfo.php')
            || $this->files->exists($this->app->configPath().'/laravelPhpInfoUi.php')
            || $this->hasPublishedViews();
    }

    public function hasPublishedViews()
    {
        return $this->files->isDirectory($this->viewsPath());
    }

    public function uiKitInstalled()
    {
        return $this->files->exists($this->app->configPath().'/ui-kit.php');
    }

    public function save(array $options, $refreshViews = false)
    {
        $directory = $this->app->configPath();
        $this->directory($directory);
        $config = $directory.'/laravelPhpInfo.php';

        if (! $this->files->exists($config) && ! $this->files->copy(__DIR__.'/config/laravelPhpInfo.php', $config)) {
            throw new RuntimeException('Could not publish the PHP Info configuration.');
        }

        $backup = $refreshViews ? $this->refreshViews() : null;
        $contents = "<?php\n\nreturn ".var_export($options, true).";\n";

        $this->writeSettings($directory.'/laravelPhpInfoUi.php', $contents);

        return $backup;
    }

    private function writeSettings($path, $contents)
    {
        $temporary = tempnam(dirname($path), 'phpinfo-');

        if ($temporary === false) {
            throw new RuntimeException('Could not create a temporary PHP Info settings file.');
        }

        if ($this->files->put($temporary, $contents) === false || ! chmod($temporary, 0644) || ! $this->files->move($temporary, $path)) {
            $this->files->delete($temporary);

            throw new RuntimeException('Could not save the PHP Info display settings.');
        }
    }

    private function refreshViews()
    {
        $destination = $this->viewsPath();
        $backup = null;

        if ($this->hasPublishedViews()) {
            $backup = $destination.'.backup-'.date('Ymd-His').'-'.uniqid();

            if (! $this->files->copyDirectory($destination, $backup)) {
                throw new RuntimeException('Could not back up the published PHP Info views. The views were not replaced.');
            }
        }

        $this->directory($destination);

        if (! $this->files->copyDirectory(__DIR__.'/resources/views', $destination)) {
            throw new RuntimeException('Could not refresh the PHP Info views. The previous views are in '.$backup);
        }

        return $backup;
    }

    private function viewsPath()
    {
        return $this->app->resourcePath().'/views/vendor/laravelPhpInfo';
    }

    private function directory($path)
    {
        if (! $this->files->isDirectory($path) && ! $this->files->makeDirectory($path, 0755, true)) {
            throw new RuntimeException('Could not create '.$path);
        }
    }
}
