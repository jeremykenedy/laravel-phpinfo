<?php

namespace jeremykenedy\LaravelPhpInfo;

class PhpInfo
{
    public function capture()
    {
        if (! function_exists('phpinfo')) {
            return ['output' => '', 'html' => false, 'available' => false];
        }

        ob_start();
        phpinfo();
        $output = ob_get_clean();

        return $this->format($output);
    }

    public function format($output)
    {
        $html = preg_match('/<body[^>]*>(.*)<\/body>/is', $output, $matches) === 1;

        return ['output' => $html ? $matches[1] : $output, 'html' => $html, 'available' => true];
    }
}
