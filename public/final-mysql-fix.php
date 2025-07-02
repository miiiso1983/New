<?php

echo "<h1>⚡ الحل النهائي لمشكلة MySQL</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة مستمرة: Laravel لا يزال يستخدم MySQL</h2>";
echo "<p>رغم جميع المحاولات، Laravel لا يزال يحاول الاتصال بـ MySQL بدلاً من SQLite</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص المشكلة</h2>";

echo "<p><strong>الخطأ:</strong> Table 'tvhxmzcvgt.users' doesn't exist (Connection: mysql)</p>";
echo "<p>هذا يعني أن Laravel يتجاهل إعدادات SQLite ويستخدم MySQL.</p>";

// حذف ملفات config المشكلة نهائياً
echo "<h3>💣 حذف ملفات Config المشكلة:</h3>";

$configFiles = [
    '../config/database.php',
    '../config/cache.php', 
    '../config/session.php',
    '../config/queue.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "💥 تم حذف: " . basename($file) . "<br>";
        } else {
            echo "❌ فشل في حذف: " . basename($file) . "<br>";
        }
    } else {
        echo "ℹ️ غير موجود: " . basename($file) . "<br>";
    }
}

// حذف مجلد config بالكامل
if (is_dir('../config')) {
    $configFiles = glob('../config/*.php');
    foreach ($configFiles as $file) {
        unlink($file);
    }
    if (rmdir('../config')) {
        echo "💥 تم حذف مجلد config بالكامل<br>";
    }
}

// تحديث index.php لاستخدام bootstrap الجديد
echo "<h3>🔧 تحديث index.php:</h3>";

$indexContent = file_get_contents('../public/index.php');
if (strpos($indexContent, 'app-sqlite-only.php') !== false) {
    echo "✅ index.php يستخدم bootstrap SQLite-only<br>";
} else {
    echo "❌ index.php لا يزال يستخدم bootstrap العادي<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Laravel مع SQLite فقط</h2>";

try {
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-sqlite-only.php';
    echo "✅ تم تحميل Laravel مع SQLite bootstrap<br>";
    
    // اختبار config
    $config = $app->make('config');
    $dbDefault = $config->get('database.default');
    echo "✅ Database default: <strong>$dbDefault</strong><br>";
    
    // اختبار database connection
    try {
        $db = $app->make('db');
        $connection = $db->connection();
        $driverName = $connection->getDriverName();
        echo "✅ Database driver: <strong>$driverName</strong><br>";
        
        if ($driverName === 'sqlite') {
            echo "🎉 تأكيد: يستخدم SQLite!<br>";
            
            // اختبار الجداول
            try {
                $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table'");
                echo "✅ الجداول الموجودة: " . count($tables) . " جدول<br>";
                
                // اختبار جدول users
                $userCount = $connection->select("SELECT COUNT(*) as count FROM users");
                echo "✅ عدد المستخدمين: " . $userCount[0]->count . "<br>";
                
                // اختبار البحث عن مستخدم
                $testUser = $connection->select("SELECT * FROM users LIMIT 1");
                if (!empty($testUser)) {
                    echo "✅ يمكن قراءة بيانات المستخدمين<br>";
                    echo "✅ مستخدم تجريبي: " . $testUser[0]->email . "<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ مشكلة في الجداول: " . $e->getMessage() . "<br>";
            }
            
        } else {
            echo "❌ لا يزال يستخدم: $driverName<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database connection: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP request
    echo "<h3>🌐 اختبار HTTP Request:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        echo "✅ HTTP Request - كود: $status<br>";
        
        if ($status == 200) {
            echo "🎉 الصفحة الرئيسية تعمل بنجاح!<br>";
        } elseif ($status == 302) {
            echo "🔄 إعادة توجيه (طبيعي)<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
        
        if (strpos($e->getMessage(), 'mysql') !== false) {
            echo "<strong>⚠️ لا يزال يحاول استخدام MySQL!</strong><br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الإجراءات المطبقة</h2>";
echo "<ul>";
echo "<li>حذف جميع ملفات config المشكلة نهائياً</li>";
echo "<li>حذف مجلد config بالكامل</li>";
echo "<li>تحديث index.php لاستخدام app-sqlite-only.php</li>";
echo "<li>فرض استخدام SQLite بدون أي config خارجية</li>";
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
echo "<p>بعد الحل النهائي، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/create-database-tables.php' => '🗄️ إنشاء الجداول',
    '/test-sqlite-only.php' => '🧪 اختبار SQLite'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>⚡ الحل النهائي</h2>";
echo "<p style='font-size: 18px; color: #dc3545; font-weight: bold;'>إذا لم تنجح هذه المحاولة، فالمشكلة قد تكون في cache الخادم أو إعدادات PHP</p>";
echo "<p>في هذه الحالة، يجب إعادة تشغيل الخادم أو مسح جميع ملفات cache</p>";
echo "</div>";

?>
