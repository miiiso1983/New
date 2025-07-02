<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel with SQLite-only configuration
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app-sqlite-only.php';

    // استخدام الطريقة التقليدية لمعالجة الطلبات
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);

} catch (TypeError $e) {
    // معالجة خطأ method_exists
    echo "<!DOCTYPE html><html><head><title>خطأ في النظام</title><meta charset='UTF-8'></head><body>";
    echo "<h1>خطأ في النظام</h1>";
    echo "<p>تم اكتشاف خطأ في النوع: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='/fix-mysql-to-sqlite.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>إصلاح قاعدة البيانات</a></p>";
    echo "<p><a href='/ultimate-fix.php' style='background:#28a745;color:white;padding:10px;text-decoration:none;border-radius:5px;'>الحل الشامل</a></p>";
    echo "</body></html>";
} catch (Exception $e) {
    // معالجة الأخطاء العامة
    echo "<!DOCTYPE html><html><head><title>خطأ في النظام</title><meta charset='UTF-8'></head><body>";
    echo "<h1>خطأ في النظام</h1>";
    echo "<p>رسالة الخطأ: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>الملف: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>السطر: " . $e->getLine() . "</p>";
    
    if (strpos($e->getMessage(), 'mysql') !== false || strpos($e->getMessage(), 'cache') !== false) {
        echo "<p><strong>مشكلة في قاعدة البيانات أو Cache</strong></p>";
        echo "<p><a href='/fix-mysql-to-sqlite.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>إصلاح قاعدة البيانات</a></p>";
    }
    
    echo "<p><a href='/ultimate-fix.php' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>الحل الشامل</a></p>";
    echo "</body></html>";
}
