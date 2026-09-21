<?php

namespace jeremykenedy\LaravelPhpInfo\Tests;

use Closure;

class RejectAccess
{
    public function handle($request, Closure $next)
    {
        abort(403);
    }
}
