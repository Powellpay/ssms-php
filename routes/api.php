<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('api.version', 'v1'))->group(function () {
    foreach (glob(__DIR__.'/../app/Domain/*/routes/*/_index.php') as $routeFile) {
        require $routeFile;
    }
});
