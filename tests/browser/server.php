<?php

require __DIR__.'/../../vendor/autoload.php';

use Illuminate\Http\Request;
use jeremykenedy\LaravelPhpInfo\LaravelPhpInfoServiceProvider;
use jeremykenedy\LaravelPhpInfo\PhpInfo;
use jeremykenedy\LaravelPhpInfo\Tests\BrowserPhpInfo;
use Orchestra\Testbench\Foundation\Application;

$app = Application::create(null, null, ['extra' => ['providers' => [LaravelPhpInfoServiceProvider::class]]]);
$app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
$app['config']->set('session.driver', 'array');
$app['config']->set('laravelPhpInfo.authEnabled', false);
$app['config']->set('laravelPhpInfoUi.view', 'standalone');
$app['config']->set('laravelPhpInfoUi.cssFramework', isset($_GET['css']) ? $_GET['css'] : 'bootstrap4');

if (! isset($_GET['real'])) {
    $app->bind(PhpInfo::class, function () {
        return new BrowserPhpInfo;
    });
}

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
