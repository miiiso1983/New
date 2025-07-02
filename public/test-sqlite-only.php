<?php

echo "<h1>🧪 اختبار SQLite فقط</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚀 اختبار Laravel مع SQLite فقط (بدون env)</h2>";

// إنشاء ملف قاعدة البيانات إذا لم يكن موجوداً
$dbPath = '/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite';
$dbDir = dirname($dbPath);

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
    echo "✅ تم إنشاء مجلد database<br>";
}

if (!file_exists($dbPath)) {
    touch($dbPath);
    chmod($dbPath, 0664);
    echo "✅ تم إنشاء ملف SQLite<br>";
} else {
    echo "✅ ملف SQLite موجود<br>";
}

// إنشاء مجلدات cache و sessions
$directories = [
    '../storage/framework/cache/data',
    '../storage/framework/sessions',
    '../storage/framework/views'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ تم إنشاء: $dir<br>";
    } else {
        echo "✅ موجود: $dir<br>";
    }
}

try {
    echo "<h3>📁 تحميل Laravel مع bootstrap SQLite:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-sqlite-only.php';
    echo "✅ تم تحميل Laravel مع SQLite bootstrap<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        // فحص إعدادات قاعدة البيانات
        $dbDefault = $config->get('database.default');
        echo "✅ قاعدة البيانات الافتراضية: <strong>$dbDefault</strong><br>";
        
        $dbConfig = $config->get('database.connections.sqlite');
        if ($dbConfig) {
            echo "✅ إعدادات SQLite:<br>";
            echo "  - Driver: " . $dbConfig['driver'] . "<br>";
            echo "  - Database: " . $dbConfig['database'] . "<br>";
        }
        
        // فحص إعدادات cache
        $cacheDefault = $config->get('cache.default');
        echo "✅ Cache driver: <strong>$cacheDefault</strong><br>";
        
        // فحص إعدادات session
        $sessionDriver = $config->get('session.driver');
        echo "✅ Session driver: <strong>$sessionDriver</strong><br>";
        
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
            
            // التأكد من أنه SQLite
            $driverName = $connection->getDriverName();
            echo "✅ Database driver: <strong>$driverName</strong><br>";
            
            if ($driverName === 'sqlite') {
                echo "🎉 تم تأكيد استخدام SQLite!<br>";
                
                $result = $connection->select('SELECT 1 as test');
                echo "✅ اتصال SQLite يعمل بنجاح<br>";
                
            } else {
                echo "❌ لا يزال يستخدم: $driverName<br>";
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
            } else {
                echo "الملف: " . $e->getFile() . "<br>";
                echo "السطر: " . $e->getLine() . "<br>";
            }
        }
        
        // اختبار صفحة تسجيل الدخول
        echo "<h3>🔐 اختبار صفحة تسجيل الدخول:</h3>";
        
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        
        try {
            $loginResponse = $kernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول - كود: $loginStatus<br>";
            
            if ($loginStatus == 200) {
                echo "🎉 صفحة تسجيل الدخول تعمل بنجاح مع SQLite!<br>";
            } elseif ($loginStatus == 302) {
                $location = $loginResponse->headers->get('Location');
                echo "🔄 إعادة توجيه من تسجيل الدخول إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في صفحة تسجيل الدخول: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'mysql') !== false) {
                echo "<strong>⚠️ لا يزال يحاول استخدام MySQL في تسجيل الدخول!</strong><br>";
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
echo "<h2>✅ Bootstrap SQLite-Only</h2>";
echo "<p>تم إنشاء bootstrap جديد يستخدم SQLite فقط بدون أي اعتماد على env() أو إعدادات خارجية.</p>";
echo "<ul>";
echo "<li>database.default = 'sqlite' (ثابت)</li>";
echo "<li>cache.default = 'file' (ثابت)</li>";
echo "<li>session.driver = 'file' (ثابت)</li>";
echo "<li>لا يوجد أي إعدادات MySQL</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔗 اختبار الملفات الجديدة</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/index-sqlite-only.php' => '🏠 الفهرس SQLite فقط',
    '/test-sqlite-only.php' => '🧪 اختبار SQLite فقط',
    '/fix-mysql-to-sqlite.php' => '🔧 إصلاح MySQL إلى SQLite',
    '/ultimate-fix.php' => '🚀 الحل الشامل'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
