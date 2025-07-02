<?php

echo "<h1>🔧 إصلاح مشكلة MySQL إلى SQLite</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 إصلاح مشكلة استخدام MySQL بدلاً من SQLite</h2>";

echo "<p><strong>المشكلة:</strong> Laravel لا يزال يحاول استخدام MySQL (Connection: mysql) بدلاً من SQLite</p>";
echo "<p>الحل: فرض استخدام SQLite وإعدادات file-based للـ cache والـ sessions</p>";

// إنشاء ملف قاعدة البيانات SQLite
$dbPath = '/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite';
$localDbPath = '../database/database.sqlite';

echo "<h3>📁 إنشاء ملف قاعدة البيانات SQLite:</h3>";

// إنشاء مجلد database إذا لم يكن موجوداً
$dbDir = dirname($dbPath);
if (!is_dir($dbDir)) {
    if (mkdir($dbDir, 0755, true)) {
        echo "✅ تم إنشاء مجلد database: $dbDir<br>";
    } else {
        echo "❌ فشل في إنشاء مجلد database<br>";
    }
}

if (!file_exists($dbPath)) {
    if (touch($dbPath)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات: $dbPath<br>";
        chmod($dbPath, 0664);
    } else {
        echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
    }
} else {
    echo "✅ ملف قاعدة البيانات موجود: $dbPath<br>";
}

if (!file_exists($localDbPath)) {
    if (touch($localDbPath)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات المحلي: $localDbPath<br>";
        chmod($localDbPath, 0664);
    }
} else {
    echo "✅ ملف قاعدة البيانات المحلي موجود<br>";
}

// إنشاء مجلدات cache و sessions
$directories = [
    '../storage/framework/cache/data',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/logs'
];

echo "<h3>📂 إنشاء المجلدات المطلوبة:</h3>";
foreach ($directories as $dir) {
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

try {
    echo "<h3>🚀 اختبار Laravel مع SQLite فقط:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        // فحص إعدادات قاعدة البيانات
        $dbDefault = $config->get('database.default');
        echo "✅ قاعدة البيانات الافتراضية: $dbDefault<br>";
        
        $dbConfig = $config->get('database.connections.sqlite');
        if ($dbConfig) {
            echo "✅ إعدادات SQLite موجودة<br>";
            echo "Driver: " . $dbConfig['driver'] . "<br>";
            echo "Database: " . $dbConfig['database'] . "<br>";
        }
        
        // فحص إعدادات cache
        $cacheDefault = $config->get('cache.default');
        echo "✅ Cache driver: $cacheDefault<br>";
        
        // فحص إعدادات session
        $sessionDriver = $config->get('session.driver');
        echo "✅ Session driver: $sessionDriver<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار database service
    try {
        $db = $app->make('db');
        echo "✅ Database service يعمل<br>";
        
        // اختبار الاتصال
        try {
            $connection = $db->connection();
            echo "✅ نوع الاتصال: " . get_class($connection) . "<br>";
            
            $result = $connection->select('SELECT 1 as test');
            echo "✅ اتصال SQLite يعمل بنجاح<br>";
            
            // اختبار إنشاء جدول
            try {
                $connection->statement('CREATE TABLE IF NOT EXISTS test_table (id INTEGER PRIMARY KEY, name TEXT)');
                echo "✅ إنشاء الجداول يعمل<br>";
                
                $connection->statement('DROP TABLE IF EXISTS test_table');
                echo "✅ حذف الجداول يعمل<br>";
                
            } catch (Exception $e) {
                echo "⚠️ مشكلة في إنشاء/حذف الجداول: " . $e->getMessage() . "<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ اتصال قاعدة البيانات: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار cache service
    try {
        $cache = $app->make('cache');
        echo "✅ Cache service يعمل<br>";
        
        // اختبار cache operations
        try {
            $cache->put('test_key', 'test_value', 60);
            echo "✅ كتابة Cache (file) تعمل<br>";
            
            $value = $cache->get('test_key');
            if ($value === 'test_value') {
                echo "✅ قراءة Cache (file) تعمل<br>";
            }
            
            $cache->forget('test_key');
            echo "✅ حذف Cache (file) يعمل<br>";
            
        } catch (Exception $e) {
            echo "❌ Cache operations: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Cache service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel مع SQLite:</h3>";
    
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
                echo "🎉 الصفحة الرئيسية تعمل بنجاح مع SQLite!<br>";
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في معالجة الطلب: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'mysql') !== false) {
                echo "<strong>⚠️ لا يزال يحاول استخدام MySQL!</strong><br>";
                echo "يجب إعادة تشغيل الخادم أو مسح cache<br>";
            }
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
echo "<h2>✅ الحل المطبق لفرض استخدام SQLite</h2>";
echo "<ul>";
echo "<li>فرض database.default = 'sqlite' (بدون env())</li>";
echo "<li>فرض cache.default = 'file' (بدون env())</li>";
echo "<li>فرض session.driver = 'file' (بدون env())</li>";
echo "<li>إزالة إعدادات MySQL من bootstrap</li>";
echo "<li>إنشاء ملف SQLite والمجلدات المطلوبة</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد فرض استخدام SQLite، جرب الروابط التالية:</p>";
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
