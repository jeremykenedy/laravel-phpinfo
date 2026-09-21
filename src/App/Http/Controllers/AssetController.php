<?php

namespace jeremykenedy\LaravelPhpInfo\App\Http\Controllers;

use Illuminate\Routing\Controller as RoutingController;

class AssetController extends RoutingController
{
    public function show($file)
    {
        $types = ['php-info.css' => 'text/css', 'php-info.js' => 'application/javascript'];
        abort_unless(isset($types[$file]), 404);
        $directory = $file === 'php-info.css' ? 'css' : 'js';

        return response(file_get_contents(__DIR__.'/../../../resources/assets/'.$directory.'/'.$file), 200, [
            'Content-Type' => $types[$file].'; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
