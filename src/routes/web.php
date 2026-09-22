<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'phpinfo', 'as' => 'laravelPhpInfo::'], function () {
    Route::get('/', 'jeremykenedy\\LaravelPhpInfo\\App\\Http\\Controllers\\LaravelPhpInfoController@phpinfo')->name('phpinfo');
    Route::get('assets/{file}', 'jeremykenedy\\LaravelPhpInfo\\App\\Http\\Controllers\\AssetController@show')
        ->where('file', 'php-info\\.(css|js)')->name('asset');
});
