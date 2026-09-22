<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use jeremykenedy\LaravelPhpInfo\PhpInfo;

class BrowserPhpInfo extends PhpInfo
{
    public function capture()
    {
        return $this->format(file_get_contents(__DIR__.'/fixtures/phpinfo.html'));
    }
}
