<?php

use Illuminate\Foundation\Application;

// إنشاء التطبيق بطريقة متوافقة مع Laravel 10
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// تسجيل config repository يدوياً قبل أي شيء آخر
$app->singleton('config', function () {
    return new \Illuminate\Config\Repository();
});

// تحميل ملفات الإعدادات
$configPath = $app->configPath();
if (is_dir($configPath)) {
    $config = $app->make('config');

    // تحميل ملفات الإعدادات الأساسية
    $configFiles = ['app', 'database', 'session', 'cache', 'filesystems'];
    foreach ($configFiles as $configFile) {
        $configFilePath = $configPath . '/' . $configFile . '.php';
        if (file_exists($configFilePath)) {
            $config->set($configFile, require $configFilePath);
        }
    }
}

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

// تسجيل جميع Service Providers المطلوبة بالترتيب الصحيح
$app->register(Illuminate\Foundation\Providers\FoundationServiceProvider::class);
$app->register(Illuminate\Cache\CacheServiceProvider::class);
$app->register(Illuminate\Database\DatabaseServiceProvider::class);
$app->register(Illuminate\Encryption\EncryptionServiceProvider::class);
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(Illuminate\Session\SessionServiceProvider::class);
$app->register(Illuminate\View\ViewServiceProvider::class);
$app->register(Illuminate\Cookie\CookieServiceProvider::class);
$app->register(Illuminate\Validation\ValidationServiceProvider::class);
$app->register(Illuminate\Auth\AuthServiceProvider::class);
$app->register(Illuminate\Translation\TranslationServiceProvider::class);
$app->register(Illuminate\Hashing\HashServiceProvider::class);

// تسجيل Application Service Providers
if (class_exists(App\Providers\AppServiceProvider::class)) {
    $app->register(App\Providers\AppServiceProvider::class);
}
if (class_exists(App\Providers\RouteServiceProvider::class)) {
    $app->register(App\Providers\RouteServiceProvider::class);
}

return $app;
