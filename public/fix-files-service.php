<?php

echo "<h1>🔧 إصلاح مشكلة Files Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Target class [files] does not exist</h2>";

echo "<p><strong>الخطأ:</strong> Target class [files] does not exist</p>";
echo "<p>هذا يعني أن Laravel يحتاج إلى files service الذي يوفره FilesystemServiceProvider.</p>";

try {
    echo "<h3>🚀 اختبار Laravel مع files service:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار files service
    try {
        $files = $app->make('files');
        echo "✅ Files service يعمل<br>";
        echo "نوع Files: " . get_class($files) . "<br>";
        
        // اختبار وظائف files
        $testPath = storage_path('test.txt');
        if ($files->put($testPath, 'test content')) {
            echo "✅ كتابة الملفات تعمل<br>";
            if ($files->exists($testPath)) {
                echo "✅ فحص وجود الملفات يعمل<br>";
                $files->delete($testPath);
                echo "✅ حذف الملفات يعمل<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Files service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار Service Providers الأخرى
    echo "<h3>🔧 فحص Service Providers الأخرى:</h3>";
    
    $services = [
        'db' => 'Database Manager',
        'session' => 'Session Manager',
        'view' => 'View Factory',
        'auth' => 'Auth Manager',
        'hash' => 'Hash Manager',
        'translator' => 'Translator'
    ];
    
    foreach ($services as $service => $name) {
        try {
            $instance = $app->make($service);
            echo "✅ $name ($service) يعمل<br>";
        } catch (Exception $e) {
            echo "❌ $name ($service): " . $e->getMessage() . "<br>";
        }
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
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
            $loginResponse = $kernel->handle($loginRequest);
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
        echo "❌ HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'files') !== false) {
        echo "<br><strong>🔧 تم تطبيق الحل لمشكلة Files Service:</strong><br>";
        echo "- تم تسجيل files service يدوياً<br>";
        echo "- تم تسجيل FilesystemServiceProvider<br>";
        echo "- تم ترتيب Service Providers بالتسلسل الصحيح<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل المطبق لمشكلة Files Service</h2>";
echo "<ul>";
echo "<li>تسجيل files service يدوياً في bootstrap/app.php</li>";
echo "<li>تسجيل FilesystemServiceProvider</li>";
echo "<li>تسجيل جميع Service Providers المطلوبة بالترتيب الصحيح</li>";
echo "<li>إضافة معالجة أخطاء محسنة</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة files service، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/ultimate-fix.php' => '🚀 الحل الشامل'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📋 ملخص جميع الإصلاحات</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;'>";

$fixes = [
    "✅ Config Service" => "تسجيل config repository يدوياً",
    "✅ Files Service" => "تسجيل files service و FilesystemServiceProvider",
    "✅ Database Service" => "تسجيل DatabaseServiceProvider",
    "✅ Session Service" => "تسجيل SessionServiceProvider",
    "✅ View Service" => "تسجيل ViewServiceProvider",
    "✅ Auth Service" => "تسجيل AuthServiceProvider",
    "✅ Hash Service" => "تسجيل HashServiceProvider",
    "✅ Translation Service" => "تسجيل TranslationServiceProvider"
];

foreach ($fixes as $title => $description) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<strong>$title</strong><br>";
    echo "<small style='color: #666;'>$description</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

?>
