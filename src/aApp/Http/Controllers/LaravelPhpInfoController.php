<?php

namespace jeremykenedy\laravelPhpInfo\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaravelPhpInfoController extends Controller
{
    private $_authEnabled;
    private $_rolesEnabled;
    private $_rolesMiddlware;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('web');

        $this->_authEnabled = config('laravelPhpInfo.authEnabled');
        $this->_rolesEnabled = config('laravelPhpInfo.rolesEnabled');
        $this->_rolesMiddlware = config('laravelPhpInfo.rolesMiddlware');

        if ($this->_authEnabled) {
            $this->middleware('auth');
        }

        if ($this->_rolesEnabled) {
            $this->middleware($this->_rolesMiddlware);
        }
    }

    /**
     * Display php info page.
     *
     * @return \Illuminate\Http\Response
     */
    public function phpinfo()
    {
        return View('laravelPhpInfo::phpinfo.php-info');
    }
}
