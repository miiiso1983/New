<?php

use Illuminate\Foundation\Application;

// إنشاء التطبيق بطريقة متوافقة مع Laravel 10
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// تسجيل Service Providers الأساسية
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// تسجيل Service Providers المطلوبة
$app->register(Illuminate\Database\DatabaseServiceProvider::class);
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(Illuminate\View\ViewServiceProvider::class);
$app->register(Illuminate\Session\SessionServiceProvider::class);
$app->register(Illuminate\Cookie\CookieServiceProvider::class);
$app->register(Illuminate\Encryption\EncryptionServiceProvider::class);
$app->register(Illuminate\Validation\ValidationServiceProvider::class);
$app->register(Illuminate\Auth\AuthServiceProvider::class);

return $app;
