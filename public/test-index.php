<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

echo "<h1>اختبار Laravel</h1>";

// فحص الملفات الأساسية
echo "<h2>فحص الملفات الأساسية:</h2>";

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    echo "⚠️ الموقع في وضع الصيانة<br>";
    require $maintenance;
}

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo "✅ Composer autoload موجود<br>";
    require __DIR__.'/../vendor/autoload.php';
} else {
    echo "❌ Composer autoload غير موجود<br>";
    exit;
}

if (file_exists(__DIR__.'/../bootstrap/app.php')) {
    echo "✅ Bootstrap app موجود<br>";
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';
} else {
    echo "❌ Bootstrap app غير موجود<br>";
    exit;
}

echo "<h2>معلومات Laravel:</h2>";
try {
    echo "إصدار Laravel: " . $app->version() . "<br>";
    echo "البيئة: " . $app->environment() . "<br>";
    echo "وضع التشخيص: " . (config('app.debug') ? 'مفعل' : 'معطل') . "<br>";
    echo "الرابط الأساسي: " . config('app.url') . "<br>";
    
    echo "<h2>اختبار قاعدة البيانات:</h2>";
    try {
        $pdo = new PDO('sqlite:' . database_path('database.sqlite'));
        echo "✅ الاتصال بقاعدة البيانات نجح<br>";
    } catch (Exception $e) {
        echo "❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>اختبار الطلب:</h2>";
    $request = Request::capture();
    echo "الطريقة: " . $request->method() . "<br>";
    echo "المسار: " . $request->path() . "<br>";
    echo "الرابط الكامل: " . $request->fullUrl() . "<br>";
    
    echo "<h2>محاولة تشغيل Laravel:</h2>";
    try {
        $response = $app->handleRequest($request);
        echo "✅ Laravel يعمل بنجاح!<br>";
        echo "<a href='/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>الذهاب للموقع</a>";
    } catch (Exception $e) {
        echo "❌ خطأ في تشغيل Laravel: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

?>
