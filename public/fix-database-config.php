<?php

echo "<h1>🔧 إصلاح إعدادات قاعدة البيانات</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة قاعدة البيانات</h2>";

echo "<p><strong>الخطأ:</strong> SQLSTATE[42S02]: Base table or view not found: 1146 Table 'tvhxmzcvgt.cache' doesn't exist</p>";
echo "<p>هذا يعني أن Laravel يحاول استخدام MySQL بدلاً من SQLite، أو أن جداول cache/sessions غير موجودة.</p>";

// فحص ملف قاعدة البيانات SQLite
$dbPath = '/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite';
$localDbPath = '../database/database.sqlite';

echo "<h3>📁 فحص ملف قاعدة البيانات:</h3>";

if (file_exists($dbPath)) {
    echo "✅ ملف قاعدة البيانات موجود: $dbPath<br>";
    echo "حجم الملف: " . filesize($dbPath) . " بايت<br>";
} else {
    echo "❌ ملف قاعدة البيانات غير موجود: $dbPath<br>";
    
    // محاولة إنشاء الملف
    if (touch($dbPath)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
        chmod($dbPath, 0664);
    } else {
        echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
    }
}

if (file_exists($localDbPath)) {
    echo "✅ ملف قاعدة البيانات المحلي موجود: $localDbPath<br>";
} else {
    echo "❌ ملف قاعدة البيانات المحلي غير موجود<br>";
    if (touch($localDbPath)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات المحلي<br>";
        chmod($localDbPath, 0664);
    }
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
    echo "<h3>🚀 اختبار Laravel مع إعدادات قاعدة البيانات المحسنة:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        // فحص إعدادات قاعدة البيانات
        $dbConnection = $config->get('database.default');
        echo "✅ اتصال قاعدة البيانات الافتراضي: $dbConnection<br>";
        
        $dbConfig = $config->get("database.connections.$dbConnection");
        if ($dbConfig) {
            echo "✅ إعدادات قاعدة البيانات موجودة<br>";
            echo "Driver: " . ($dbConfig['driver'] ?? 'غير محدد') . "<br>";
            echo "Database: " . ($dbConfig['database'] ?? 'غير محدد') . "<br>";
        }
        
        // فحص إعدادات cache
        $cacheDriver = $config->get('cache.default');
        echo "✅ Cache driver: $cacheDriver<br>";
        
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
            $result = $connection->select('SELECT 1 as test');
            echo "✅ اتصال قاعدة البيانات يعمل<br>";
            
            // فحص الجداول الموجودة
            try {
                $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table'");
                echo "✅ الجداول الموجودة: " . count($tables) . " جدول<br>";
                foreach ($tables as $table) {
                    echo "  - " . $table->name . "<br>";
                }
            } catch (Exception $e) {
                echo "⚠️ لا يمكن قراءة الجداول: " . $e->getMessage() . "<br>";
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
            echo "✅ كتابة Cache تعمل<br>";
            
            $value = $cache->get('test_key');
            if ($value === 'test_value') {
                echo "✅ قراءة Cache تعمل<br>";
            }
            
            $cache->forget('test_key');
            echo "✅ حذف Cache يعمل<br>";
            
        } catch (Exception $e) {
            echo "❌ Cache operations: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Cache service: " . $e->getMessage() . "<br>";
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
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل المطبق لمشاكل قاعدة البيانات</h2>";
echo "<ul>";
echo "<li>تغيير CACHE_STORE من database إلى file</li>";
echo "<li>تغيير SESSION_DRIVER من database إلى file</li>";
echo "<li>تصحيح مسار قاعدة البيانات SQLite</li>";
echo "<li>إنشاء مجلدات cache و sessions المطلوبة</li>";
echo "<li>تغيير QUEUE_CONNECTION من database إلى sync</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح إعدادات قاعدة البيانات، جرب الروابط التالية:</p>";
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
