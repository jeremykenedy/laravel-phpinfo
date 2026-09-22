<?php

namespace jeremykenedy\LaravelPhpInfo\Console;

class UpdateCommand extends SetupCommand
{
    protected $signature = 'phpinfo:update
        {--css= : bootstrap3, bootstrap4, bootstrap5, or tailwind}
        {--frontend= : Page renderer (blade)}
        {--view= : app or standalone}
        {--theme= : system, light, or dark}
        {--ui-kit : Also configure an installed Laravel UI Kit package}
        {--refresh-views : Back up and replace published package templates}';

    protected $description = 'Update PHP Info display settings without replacing application configuration';

    protected $operation = 'update';
}
