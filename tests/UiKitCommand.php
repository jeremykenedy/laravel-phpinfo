<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use Illuminate\Console\Command;

class UiKitCommand extends Command
{
    protected $signature = 'ui-kit:install {--css=} {--frontend=}';

    public $receivedCss;

    public $receivedFrontend;

    public $status = 0;

    public function handle()
    {
        $this->receivedCss = $this->option('css');
        $this->receivedFrontend = $this->option('frontend');

        return $this->status;
    }
}
