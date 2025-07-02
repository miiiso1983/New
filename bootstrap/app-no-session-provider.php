<?php

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as ConfigRepository;

// إنشاء التطبيق
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// إنشاء config service
$config = new ConfigRepository();

// إعدادات app
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

// إعدادات database
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

// إعدادات cache
$config->set('cache', [
    'default' => 'file',
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => dirname(__DIR__) . '/storage/framework/cache/data',
        ],
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ],
    'prefix' => 'laravel_cache',
]);

// إعدادات translation
$config->set('translation', [
    'locale' => 'ar',
    'fallback_locale' => 'en',
    'path' => dirname(__DIR__) . '/lang',
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

// تسجيل Service Providers الأساسية (بدون SessionServiceProvider)
$app->register(\Illuminate\Foundation\Providers\FoundationServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);
$app->register(\Illuminate\Translation\TranslationServiceProvider::class);
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Validation\ValidationServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);
$app->register(\Illuminate\Hashing\HashServiceProvider::class);

// إنشاء Session services يدوياً بالكامل بدون SessionServiceProvider
$sessionPath = dirname(__DIR__) . '/storage/framework/sessions';

// إنشاء مجلد sessions إذا لم يكن موجوداً
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0755, true);
}

// تسجيل Session Handler يدوياً
$app->singleton('session.handler', function ($app) use ($sessionPath) {
    $files = $app->make('files');
    return new \Illuminate\Session\FileSessionHandler($files, $sessionPath, 120);
});

// تسجيل Session Store يدوياً
$app->singleton('session.store', function ($app) {
    $handler = $app->make('session.handler');
    $store = new \Illuminate\Session\Store('laravel_session', $handler);
    $store->start();
    return $store;
});

// تسجيل Session Manager يدوياً (بدون driver resolution)
$app->singleton('session', function ($app) {
    return $app->make('session.store');
});

// تسجيل Session Contract
$app->singleton(\Illuminate\Contracts\Session\Session::class, function ($app) {
    return $app->make('session.store');
});

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
