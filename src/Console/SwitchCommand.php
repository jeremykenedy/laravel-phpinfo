<?php

namespace jeremykenedy\LaravelPhpInfo\Console;

class SwitchCommand extends SetupCommand
{
    protected $signature = 'phpinfo:switch
        {--css= : bootstrap3, bootstrap4, bootstrap5, or tailwind}
        {--frontend= : Page renderer (blade)}
        {--view= : app or standalone}
        {--theme= : system, light, or dark}
        {--ui-kit : Also configure an installed Laravel UI Kit package}
        {--refresh-views : Back up and replace published package templates}';

    protected $description = 'Switch the PHP Info CSS framework, layout, or color scheme';

    protected $operation = 'switch';
}
