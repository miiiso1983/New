<?php

echo "<h1>🔧 الحل النهائي لمشكلة Config Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚀 تطبيق الحل النهائي</h2>";

try {
    echo "<p>📁 تحميل Composer autoload...</p>";
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer بنجاح<br>";
    
    echo "<p>🏗️ تحميل Laravel application مع config service محسن...</p>";
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    // فحص config service
    echo "<h3>🔧 فحص Config Service:</h3>";
    
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل بنجاح<br>";
        echo "نوع Config: " . get_class($config) . "<br>";
        
        // اختبار قراءة إعدادات
        try {
            $appName = $config->get('app.name', 'Laravel');
            echo "✅ قراءة الإعدادات تعمل - اسم التطبيق: $appName<br>";
        } catch (Exception $e) {
            echo "⚠️ مشكلة في قراءة الإعدادات: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Config service لا يعمل: " . $e->getMessage() . "<br>";
        
        // محاولة إنشاء config service يدوياً
        echo "🔧 محاولة إنشاء config service يدوياً...<br>";
        try {
            $config = new \Illuminate\Config\Repository();
            $app->instance('config', $config);
            echo "✅ تم إنشاء config service يدوياً<br>";
        } catch (Exception $e2) {
            echo "❌ فشل في إنشاء config service: " . $e2->getMessage() . "<br>";
        }
    }
    
    // فحص Service Providers الأخرى
    echo "<h3>🔧 فحص Service Providers الأخرى:</h3>";
    
    $services = [
        'db' => 'Database Manager',
        'cache' => 'Cache Manager',
        'session' => 'Session Manager',
        'view' => 'View Factory',
        'auth' => 'Auth Manager'
    ];
    
    foreach ($services as $service => $name) {
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
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $httpKernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ معالجة الطلبات تعمل - كود: $status<br>";
            
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
        
        try {
            $loginResponse = $httpKernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول - كود: $loginStatus<br>";
            
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
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'config') !== false) {
        echo "<br><strong>🔧 تم تطبيق الحل النهائي لمشكلة Config:</strong><br>";
        echo "- تم تسجيل config repository يدوياً قبل أي service provider<br>";
        echo "- تم تحميل ملفات الإعدادات الأساسية<br>";
        echo "- تم ترتيب Service Providers بالتسلسل الصحيح<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل النهائي المطبق</h2>";
echo "<ul>";
echo "<li>تسجيل config repository يدوياً في bootstrap/app.php</li>";
echo "<li>تحميل ملفات الإعدادات قبل Service Providers</li>";
echo "<li>ترتيب Service Providers بالتسلسل الصحيح</li>";
echo "<li>إضافة معالجة أخطاء محسنة</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد تطبيق الحل النهائي، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/final-status.php' => '📊 التقرير النهائي'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
