<?php

require __DIR__.'/../../vendor/autoload.php';

if ($argv[1] !== 'missing') {
    require __DIR__.'/host-controller-'.$argv[1].'.php.stub';
}

$class = 'jeremykenedy\LaravelPhpInfo\App\Http\Controllers\LaravelPhpInfoController';
$expected = $argv[1] === 'legacy' ? 'App\Http\Controllers\Controller' : 'Illuminate\Routing\Controller';

exit(is_subclass_of($class, $expected) ? 0 : 1);
