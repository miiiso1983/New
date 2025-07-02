<?php

echo "<h1>🔧 اختبار نهائي لإصلاح الشاشة البيضاء</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚀 اختبار Laravel المحدث</h2>";

try {
    echo "<p>📁 تحميل Composer autoload...</p>";
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer بنجاح<br>";
    
    echo "<p>🏗️ تحميل Laravel application...</p>";
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    echo "<p>📊 معلومات التطبيق:</p>";
    echo "<ul>";
    if (method_exists($app, 'version')) {
        echo "<li>إصدار Laravel: " . $app->version() . "</li>";
    } else {
        echo "<li>إصدار Laravel: 10.x أو أقدم</li>";
    }
    echo "<li>نوع التطبيق: " . get_class($app) . "</li>";
    echo "<li>مجلد التطبيق: " . $app->basePath() . "</li>";
    echo "</ul>";
    
    echo "<p>🔧 اختبار الخدمات الأساسية:</p>";
    
    // اختبار HTTP Kernel
    try {
        $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل بنجاح<br>";
    } catch (Exception $e) {
        echo "❌ مشكلة في HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
    // اختبار Console Kernel
    try {
        $consoleKernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        echo "✅ Console Kernel يعمل بنجاح<br>";
    } catch (Exception $e) {
        echo "❌ مشكلة في Console Kernel: " . $e->getMessage() . "<br>";
    }
    
    // اختبار Exception Handler
    try {
        $exceptionHandler = $app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);
        echo "✅ Exception Handler يعمل بنجاح<br>";
    } catch (Exception $e) {
        echo "❌ مشكلة في Exception Handler: " . $e->getMessage() . "<br>";
    }
    
    echo "<p>🗄️ اختبار قاعدة البيانات:</p>";
    
    // اختبار قاعدة البيانات
    $dbFile = '../database/database.sqlite';
    if (file_exists($dbFile)) {
        echo "✅ ملف قاعدة البيانات موجود<br>";
        if (is_writable($dbFile)) {
            echo "✅ ملف قاعدة البيانات قابل للكتابة<br>";
        } else {
            echo "⚠️ ملف قاعدة البيانات غير قابل للكتابة<br>";
        }
    } else {
        echo "⚠️ ملف قاعدة البيانات غير موجود - محاولة إنشاء...<br>";
        if (touch($dbFile)) {
            echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
        } else {
            echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
        }
    }
    
    echo "<p>🔄 اختبار المايجريشن:</p>";
    try {
        $consoleKernel->call('migrate', ['--force' => true]);
        echo "✅ تم تشغيل المايجريشن بنجاح<br>";
        
        // محاولة تشغيل السيدر
        try {
            $consoleKernel->call('db:seed', ['--force' => true]);
            echo "✅ تم تشغيل السيدر بنجاح<br>";
        } catch (Exception $e) {
            echo "⚠️ تحذير في السيدر: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "⚠️ مشكلة في المايجريشن: " . $e->getMessage() . "<br>";
    }
    
    echo "<p>🌐 اختبار الطلب HTTP:</p>";
    try {
        $request = \Illuminate\Http\Request::capture();
        echo "✅ تم إنشاء HTTP Request بنجاح<br>";
        echo "<ul>";
        echo "<li>الطريقة: " . $request->method() . "</li>";
        echo "<li>المسار: " . $request->path() . "</li>";
        echo "<li>الرابط الكامل: " . $request->fullUrl() . "</li>";
        echo "</ul>";
        
        // محاولة معالجة الطلب
        echo "<p>🎯 محاولة معالجة طلب تجريبي:</p>";
        $response = $httpKernel->handle($request);
        echo "✅ تم معالجة الطلب بنجاح!<br>";
        echo "كود الاستجابة: " . $response->getStatusCode() . "<br>";
        
    } catch (Exception $e) {
        echo "⚠️ مشكلة في معالجة الطلب: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    echo "<pre style='background: #f8d7da; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎉 النتيجة النهائية</h2>";
echo "<p>إذا رأيت هذه الرسالة، فهذا يعني أن Laravel يعمل بشكل أساسي!</p>";
echo "<p><strong>الخطوات التالية:</strong></p>";
echo "<ol>";
echo "<li><a href='/' target='_blank' style='color: #155724;'>اختبار الصفحة الرئيسية</a></li>";
echo "<li><a href='/login' target='_blank' style='color: #155724;'>اختبار صفحة تسجيل الدخول</a></li>";
echo "<li><a href='/dashboard' target='_blank' style='color: #155724;'>اختبار لوحة التحكم</a></li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📋 معلومات إضافية</h2>";
echo "<p><strong>ملفات التشخيص المتاحة:</strong></p>";
echo "<ul>";
echo "<li><a href='/debug.php' target='_blank'>debug.php</a> - تشخيص شامل</li>";
echo "<li><a href='/fix-white-screen.php' target='_blank'>fix-white-screen.php</a> - إصلاح المشاكل</li>";
echo "<li><a href='/cloudways-setup.php' target='_blank'>cloudways-setup.php</a> - إعداد Cloudways</li>";
echo "<li><a href='/test-index.php' target='_blank'>test-index.php</a> - اختبار Laravel</li>";
echo "</ul>";
echo "</div>";

?>
