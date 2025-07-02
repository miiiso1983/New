<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as ConfigRepository;

define('LARAVEL_START', microtime(true));

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // تحميل Composer
    require __DIR__.'/../vendor/autoload.php';
    
    // إنشاء التطبيق
    $app = new Application(
        $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
    );
    
    // إنشاء config service مباشرة
    $config = new ConfigRepository();
    
    // تحميل الإعدادات الأساسية
    $config->set('app', [
        'name' => env('APP_NAME', 'نظام إدارة الصيدلية'),
        'env' => env('APP_ENV', 'production'),
        'debug' => env('APP_DEBUG', false),
        'url' => env('APP_URL', 'https://phplaravel-1486247-5658490.cloudwaysapps.com'),
        'key' => env('APP_KEY'),
        'cipher' => 'AES-256-CBC',
        'timezone' => 'UTC',
        'locale' => 'ar',
        'fallback_locale' => 'en',
    ]);
    
    $config->set('database', [
        'default' => env('DB_CONNECTION', 'sqlite'),
        'connections' => [
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => database_path('database.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ],
    ]);
    
    // تسجيل config في التطبيق
    $app->instance('config', $config);
    
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
    
    // الحصول على HTTP Kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // معالجة الطلب
    $response = $kernel->handle(
        $request = Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    // عرض صفحة خطأ مخصصة
    echo "<!DOCTYPE html>";
    echo "<html lang='ar' dir='rtl'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>خطأ في النظام</title>";
    echo "<style>";
    echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }";
    echo ".error-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 600px; text-align: center; }";
    echo ".error-title { color: #e74c3c; font-size: 24px; margin-bottom: 20px; }";
    echo ".error-message { color: #333; font-size: 16px; margin-bottom: 20px; }";
    echo ".error-details { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: left; font-family: monospace; font-size: 14px; margin: 20px 0; }";
    echo ".btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 8px; margin: 10px; }";
    echo ".btn:hover { background: #2980b9; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";
    echo "<div class='error-container'>";
    echo "<h1 class='error-title'>⚠️ خطأ في النظام</h1>";
    echo "<p class='error-message'>عذراً، حدث خطأ أثناء تحميل النظام.</p>";
    echo "<div class='error-details'>";
    echo "<strong>رسالة الخطأ:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>الملف:</strong> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<strong>السطر:</strong> " . $e->getLine();
    echo "</div>";
    echo "<p>يرجى المحاولة مرة أخرى أو الاتصال بالدعم الفني.</p>";
    echo "<a href='/test-simple.php' class='btn'>🧪 اختبار النظام</a>";
    echo "<a href='/fix-config-final.php' class='btn'>🔧 إصلاح المشاكل</a>";
    echo "<a href='/debug.php' class='btn'>🔍 تشخيص شامل</a>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
}

?>
