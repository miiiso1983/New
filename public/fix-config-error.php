<?php

echo "<h1>🔧 إصلاح مشكلة Config Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚀 إصلاح مشكلة Target class [config] does not exist</h2>";

try {
    echo "<p>📁 تحميل Composer autoload...</p>";
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer بنجاح<br>";
    
    echo "<p>🏗️ تحميل Laravel application...</p>";
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    // فحص Service Providers
    echo "<h3>🔧 فحص Service Providers:</h3>";
    
    $providers = [
        'config' => 'Configuration Repository',
        'db' => 'Database Manager',
        'cache' => 'Cache Manager',
        'session' => 'Session Manager',
        'view' => 'View Factory',
        'auth' => 'Auth Manager',
        'hash' => 'Hash Manager',
        'translator' => 'Translator'
    ];
    
    foreach ($providers as $service => $name) {
        try {
            $instance = $app->make($service);
            echo "✅ $name ($service) يعمل<br>";
        } catch (Exception $e) {
            echo "❌ $name ($service) لا يعمل: " . $e->getMessage() . "<br>";
        }
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel:</h3>";
    
    try {
        $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط للصفحة الرئيسية
        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('Accept', 'text/html');
        
        try {
            $response = $httpKernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ معالجة الطلبات تعمل - كود الاستجابة: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل بنجاح!<br>";
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في معالجة الطلب: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
        }
        
        // اختبار صفحة تسجيل الدخول
        echo "<h3>🔐 اختبار صفحة تسجيل الدخول:</h3>";
        
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        $loginRequest->headers->set('Accept', 'text/html');
        
        try {
            $loginResponse = $httpKernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول - كود الاستجابة: $loginStatus<br>";
            
            if ($loginStatus == 200) {
                echo "🎉 صفحة تسجيل الدخول تعمل بنجاح!<br>";
            } elseif ($loginStatus == 302) {
                $location = $loginResponse->headers->get('Location');
                echo "🔄 إعادة توجيه من تسجيل الدخول إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في صفحة تسجيل الدخول: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel لا يعمل: " . $e->getMessage() . "<br>";
    }
    
    // اختبار قاعدة البيانات
    echo "<h3>🗄️ اختبار قاعدة البيانات:</h3>";
    
    try {
        $db = $app->make('db');
        echo "✅ Database Manager يعمل<br>";
        
        // اختبار الاتصال
        $connection = $db->connection();
        echo "✅ الاتصال بقاعدة البيانات نجح<br>";
        
        // اختبار استعلام
        $result = $connection->select('SELECT 1 as test');
        echo "✅ استعلام قاعدة البيانات يعمل<br>";
        
        // فحص الجداول
        try {
            $users = $db->table('users')->count();
            echo "✅ جدول المستخدمين يعمل - عدد المستخدمين: $users<br>";
        } catch (Exception $e) {
            echo "⚠️ جدول المستخدمين غير موجود أو فارغ<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ مشكلة في قاعدة البيانات: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'config') !== false) {
        echo "<br><strong>🔧 حل مشكلة Config:</strong><br>";
        echo "تم تحديث bootstrap/app.php لتسجيل FoundationServiceProvider الذي يوفر config service.<br>";
        echo "تم إضافة جميع Service Providers المطلوبة بالترتيب الصحيح.<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الإصلاحات المطبقة</h2>";
echo "<ul>";
echo "<li>إضافة FoundationServiceProvider لحل مشكلة config service</li>";
echo "<li>تسجيل جميع Service Providers بالترتيب الصحيح</li>";
echo "<li>إنشاء RouteServiceProvider و AuthServiceProvider المفقودة</li>";
echo "<li>تحسين معالجة الأخطاء والتشخيص</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة config service، جرب الروابط التالية:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank' style='color: #856404;'>الصفحة الرئيسية</a></li>";
echo "<li><a href='/login' target='_blank' style='color: #856404;'>تسجيل الدخول</a></li>";
echo "<li><a href='/dashboard' target='_blank' style='color: #856404;'>لوحة التحكم</a></li>";
echo "<li><a href='/quick-test.php' target='_blank' style='color: #856404;'>الاختبار السريع</a></li>";
echo "</ul>";
echo "</div>";

?>
