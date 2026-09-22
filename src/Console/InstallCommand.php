<?php

namespace jeremykenedy\LaravelPhpInfo\Console;

class InstallCommand extends SetupCommand
{
    protected $signature = 'phpinfo:install
        {--css= : bootstrap3, bootstrap4, bootstrap5, or tailwind}
        {--frontend= : Page renderer (blade)}
        {--view= : app or standalone}
        {--theme= : system, light, or dark}
        {--ui-kit : Also configure an installed Laravel UI Kit package}
        {--refresh-views : Back up and replace published package templates}
        {--force : Run setup again when already installed}';

    protected $description = 'Install Laravel PHP Info without replacing existing configuration or views';

    protected $operation = 'install';
}
