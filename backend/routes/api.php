<?php

use Illuminate\Support\Facades\Route;

foreach (glob(__DIR__.'/../app/Domain/*/routes/*/_index.php') as $routeFile) {
    require $routeFile;
}
