<?php

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as ConfigRepository;

// إنشاء التطبيق
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// إنشاء وتسجيل config service بشكل مباشر
$config = new ConfigRepository();

// تحميل الإعدادات الأساسية
$config->set('app', [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'timezone' => 'UTC',
    'locale' => 'en',
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
    'files' => storage_path('framework/sessions'),
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
            'path' => storage_path('framework/cache/data'),
        ],
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ],
    'prefix' => 'laravel_cache',
]);

$config->set('view', [
    'paths' => [
        resource_path('views'),
    ],
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
]);

$config->set('filesystems', [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],
]);

// تسجيل config في التطبيق
$app->instance('config', $config);

// تسجيل files service يدوياً
$app->singleton('files', function () {
    return new \Illuminate\Filesystem\Filesystem();
});

// تسجيل Service Providers الأساسية المطلوبة
$app->register(\Illuminate\Foundation\Providers\FoundationServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(\Illuminate\View\ViewServiceProvider::class);
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Validation\ValidationServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);
$app->register(\Illuminate\Hashing\HashServiceProvider::class);
$app->register(\Illuminate\Translation\TranslationServiceProvider::class);

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
