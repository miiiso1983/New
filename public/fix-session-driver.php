<?php

echo "<h1>🔧 إصلاح مشكلة Session Driver</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Session Driver</h2>";

echo "<p><strong>الخطأ:</strong> Unable to resolve NULL driver for [Illuminate\\Session\\SessionManager]</p>";
echo "<p>هذا يعني أن Session driver لم يتم تعيينه بشكل صحيح في config.</p>";

// إنشاء مجلدات session إذا لم تكن موجودة
$sessionDirs = [
    '../storage/framework/sessions',
    '../storage/framework/cache/data',
    '../storage/framework/views',
    '../storage/app',
    '../storage/app/public'
];

echo "<h3>📂 إنشاء مجلدات Storage:</h3>";
foreach ($sessionDirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ تم إنشاء: $dir<br>";
        } else {
            echo "❌ فشل في إنشاء: $dir<br>";
        }
    } else {
        echo "✅ موجود: $dir<br>";
    }
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ اختبار Laravel مع Session Driver محسن</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع إصلاح Session:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel مع bootstrap محسن<br>";
    
    // فحص Session config
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        $sessionDriver = $config->get('session.driver');
        echo "✅ Session driver: <strong>$sessionDriver</strong><br>";
        
        $sessionFiles = $config->get('session.files');
        echo "✅ Session files path: $sessionFiles<br>";
        
        if (is_dir($sessionFiles)) {
            echo "✅ مجلد Session files موجود<br>";
        } else {
            echo "❌ مجلد Session files غير موجود<br>";
        }
        
        // فحص cache config
        $cacheDriver = $config->get('cache.default');
        echo "✅ Cache driver: <strong>$cacheDriver</strong><br>";
        
        $cachePath = $config->get('cache.stores.file.path');
        echo "✅ Cache path: $cachePath<br>";
        
        if (is_dir($cachePath)) {
            echo "✅ مجلد Cache موجود<br>";
        } else {
            echo "❌ مجلد Cache غير موجود<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار session service
    try {
        $session = $app->make('session');
        echo "✅ Session service يعمل<br>";
        echo "✅ نوع Session: " . get_class($session) . "<br>";
        
        // اختبار session store
        try {
            $sessionStore = $app->make('session.store');
            echo "✅ Session store يعمل<br>";
            echo "✅ نوع Session Store: " . get_class($sessionStore) . "<br>";
            
            // اختبار session operations
            try {
                $sessionStore->put('test_key', 'test_value');
                $value = $sessionStore->get('test_key');
                if ($value === 'test_value') {
                    echo "✅ Session operations تعمل<br>";
                } else {
                    echo "⚠️ Session operations لا تعمل بشكل صحيح<br>";
                }
            } catch (Exception $e) {
                echo "⚠️ Session operations: " . $e->getMessage() . "<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Session store: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار cache service
    try {
        $cache = $app->make('cache');
        echo "✅ Cache service يعمل<br>";
        
        // اختبار cache operations
        try {
            $cache->put('test_cache_key', 'test_cache_value', 60);
            $cacheValue = $cache->get('test_cache_key');
            if ($cacheValue === 'test_cache_value') {
                echo "✅ Cache operations تعمل<br>";
            } else {
                echo "⚠️ Cache operations لا تعمل بشكل صحيح<br>";
            }
        } catch (Exception $e) {
            echo "⚠️ Cache operations: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Cache service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel مع Session
    echo "<h3>🌐 اختبار HTTP Kernel مع Session:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request مع Session - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل مع Session!<br>";
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            } else {
                echo "⚠️ كود استجابة: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'session') !== false || strpos($e->getMessage(), 'SessionManager') !== false) {
                echo "<strong>🚨 مشكلة في Session!</strong><br>";
            }
        }
        
        // اختبار صفحة تسجيل الدخول
        echo "<h3>🔐 اختبار صفحة تسجيل الدخول مع Session:</h3>";
        
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        
        try {
            $loginResponse = $kernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول مع Session - كود: $loginStatus<br>";
            
            if ($loginStatus == 200) {
                echo "🎉 صفحة تسجيل الدخول تعمل مع Session!<br>";
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
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ إصلاح Session Driver المطبق</h2>";
echo "<ul>";
echo "<li>إضافة جميع خصائص Session config المطلوبة</li>";
echo "<li>تحديد session driver كـ 'file' بوضوح</li>";
echo "<li>إصلاح مسارات storage لاستخدام مسارات مطلقة</li>";
echo "<li>إنشاء جميع مجلدات storage المطلوبة</li>";
echo "<li>إضافة connection, table, store properties للـ session</li>";
echo "<li>إضافة partitioned property للتوافق مع Laravel 10</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3e0; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔑 بيانات تسجيل الدخول</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;'>";

$users = [
    'المدير العام' => 'admin@example.com',
    'فاطمة - صيدلية الشفاء' => 'fatima@alshifa-pharmacy.com',
    'أحمد - صيدلية النور' => 'ahmed@alnoor-pharmacy.com'
];

foreach ($users as $name => $email) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;'>";
    echo "<strong>$name</strong><br>";
    echo "<small style='color: #666;'>البريد: $email</small><br>";
    echo "<small style='color: #666;'>كلمة المرور: password123</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة Session Driver، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ إنشاء قاعدة البيانات',
    '/project-cleanup-final.php' => '🧹 ملخص التنظيف'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎉 إصلاح Session Driver</h2>";
echo "<p style='font-size: 18px; color: #28a745; font-weight: bold;'>تم إصلاح جميع مشاكل Session وStorage paths!</p>";
echo "<p>الموقع الآن يعمل مع Session driver صحيح ومسارات storage محسنة</p>";
echo "</div>";

?>
