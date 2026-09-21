<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

class ControllerCompatibilityTest extends TestCase
{
    public function test_host_controller_behavior_is_preserved_when_supported()
    {
        $this->checkParent('legacy');
    }

    public function test_plain_host_controllers_use_the_routing_fallback()
    {
        $this->checkParent('modern');
    }

    public function test_the_package_works_without_a_host_controller()
    {
        $this->checkParent('missing');
    }

    private function checkParent($mode)
    {
        $output = [];
        $status = 0;
        exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__DIR__.'/fixtures/controller-inheritance.php').' '.escapeshellarg($mode), $output, $status);
        $this->assertSame(0, $status, implode("\n", $output));
    }
}
