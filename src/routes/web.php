<?php

/*
|--------------------------------------------------------------------------
| Laravel PHP Info
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'phpinfo','as' => 'laravelPhpInfo::','namespace' => 'jeremykenedy\LaravelPhpInfo\App\Http\Controllers'], function () {
    Route::get('/', ['uses' => 'LaravelPhpInfoController@phpinfo'])->name('phpinfo');
});
