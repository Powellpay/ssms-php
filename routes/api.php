<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    foreach (glob(__DIR__.'/../app/Domain/*/routes/*/_index.php') as $routeFile) {
        require $routeFile;
    }
});
