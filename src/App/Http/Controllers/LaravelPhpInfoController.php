<?php

namespace jeremykenedy\LaravelPhpInfo\App\Http\Controllers;

class LaravelPhpInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');

        if (config('laravelPhpInfo.authEnabled')) {
            $this->middleware('auth');
        }

        if (config('laravelPhpInfo.rolesEnabled')) {
            $this->middleware(config('laravelPhpInfo.rolesMiddlware'));
        }
    }

    public function phpinfo()
    {
        return response()->view('laravelPhpInfo::phpinfo.php-info', [], 200, [
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
