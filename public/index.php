<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Memeriksa mode pemeliharaan sebelum aplikasi dijalankan...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Memuat autoloader Composer untuk dependensi aplikasi...
require __DIR__.'/../vendor/autoload.php';

// Menyalakan Laravel lalu meneruskan permintaan HTTP...
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
