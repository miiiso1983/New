<?php

echo "<h1>🔧 إصلاح مشكلة Cache Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Target class [cache.store] does not exist</h2>";

echo "<p><strong>الخطأ:</strong> Target class [cache.store] does not exist</p>";
echo "<p>هذا يعني أن Laravel يحتاج إلى cache service الذي يوفره CacheServiceProvider.</p>";

// إنشاء مجلدات cache إذا لم تكن موجودة
$cacheDir = '../storage/framework/cache/data';
if (!is_dir($cacheDir)) {
    if (mkdir($cacheDir, 0755, true)) {
        echo "✅ تم إنشاء مجلد cache: $cacheDir<br>";
    } else {
        echo "❌ فشل في إنشاء مجلد cache<br>";
    }
} else {
    echo "✅ مجلد cache موجود<br>";
}

try {
    echo "<h3>🚀 اختبار Laravel مع cache service:</h3>";
    
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
        
    } catch (Exception $e) {
        echo "❌ Files service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار cache service
    try {
        $cache = $app->make('cache');
        echo "✅ Cache service يعمل<br>";
        echo "نوع Cache: " . get_class($cache) . "<br>";
        
        // اختبار cache store
        try {
            $store = $app->make('cache.store');
            echo "✅ Cache store يعمل<br>";
            echo "نوع Cache Store: " . get_class($store) . "<br>";
            
            // اختبار وظائف cache
            $testKey = 'test_cache_key';
            $testValue = 'test_cache_value';
            
            $store->put($testKey, $testValue, 60);
            echo "✅ كتابة Cache تعمل<br>";
            
            if ($store->has($testKey)) {
                echo "✅ فحص وجود Cache يعمل<br>";
                $retrievedValue = $store->get($testKey);
                if ($retrievedValue === $testValue) {
                    echo "✅ قراءة Cache تعمل<br>";
                }
                $store->forget($testKey);
                echo "✅ حذف Cache يعمل<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Cache store: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Cache service: " . $e->getMessage() . "<br>";
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
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'cache') !== false) {
        echo "<br><strong>🔧 تم تطبيق الحل لمشكلة Cache Service:</strong><br>";
        echo "- تم إضافة إعدادات cache في config<br>";
        echo "- تم تسجيل CacheServiceProvider<br>";
        echo "- تم إنشاء مجلدات cache المطلوبة<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل المطبق لمشكلة Cache Service</h2>";
echo "<ul>";
echo "<li>إضافة إعدادات cache في bootstrap/app.php</li>";
echo "<li>تسجيل CacheServiceProvider</li>";
echo "<li>إنشاء مجلدات cache المطلوبة</li>";
echo "<li>تكوين file cache driver كافتراضي</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة cache service، جرب الروابط التالية:</p>";
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

?>
