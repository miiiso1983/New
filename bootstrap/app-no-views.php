<?php

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as ConfigRepository;

// إنشاء التطبيق
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// إنشاء config service
$config = new ConfigRepository();

// إعدادات ثابتة
$config->set('app', [
    'name' => 'نظام إدارة الصيدلية',
    'env' => 'production',
    'debug' => false,
    'url' => 'https://phplaravel-1486247-5658490.cloudwaysapps.com',
    'key' => 'base64:QKyZoyATcjBxA0qzfcTUPrsxush+g+1ASMVMxxjXcwk=',
    'cipher' => 'AES-256-CBC',
    'timezone' => 'UTC',
    'locale' => 'ar',
    'fallback_locale' => 'en',
]);

$config->set('database', [
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => '/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],
]);

$config->set('session', [
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => dirname(__DIR__) . '/storage/framework/sessions',
    'lottery' => [2, 100],
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'http_only' => true,
    'same_site' => 'lax',
]);

$config->set('cache', [
    'default' => 'file',
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => dirname(__DIR__) . '/storage/framework/cache/data',
        ],
    ],
    'prefix' => 'laravel_cache',
]);

// تسجيل config
$app->instance('config', $config);

// تسجيل files service
$app->singleton('files', function () {
    return new \Illuminate\Filesystem\Filesystem();
});

// تهيئة Facades
\Illuminate\Support\Facades\Facade::clearResolvedInstances();
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

// تسجيل Service Providers الأساسية (بدون ViewServiceProvider)
$app->register(\Illuminate\Foundation\Providers\FoundationServiceProvider::class);
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(\Illuminate\Validation\ValidationServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);
$app->register(\Illuminate\Hashing\HashServiceProvider::class);

// تسجيل MaintenanceMode service يدوياً
$app->singleton(
    \Illuminate\Contracts\Foundation\MaintenanceMode::class,
    \Illuminate\Foundation\MaintenanceModeManager::class
);

// تسجيل الخدمات الأساسية
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

// تسجيل Application Service Providers
if (class_exists('App\Providers\AppServiceProvider')) {
    $app->register(App\Providers\AppServiceProvider::class);
}
if (class_exists('App\Providers\RouteServiceProvider')) {
    $app->register(App\Providers\RouteServiceProvider::class);
}

return $app;
