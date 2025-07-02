<?php

echo "<h1>🔧 إصلاح بسيط للموقع</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h2>🚀 إصلاح المشاكل الأساسية</h2>";

// 1. إنشاء قاعدة البيانات
echo "<h3>1️⃣ إنشاء قاعدة البيانات:</h3>";
$dbFile = '../database/database.sqlite';
if (!file_exists($dbFile)) {
    if (touch($dbFile)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
        chmod($dbFile, 0664);
    } else {
        echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
    }
} else {
    echo "✅ ملف قاعدة البيانات موجود<br>";
}

// 2. إنشاء المجلدات المطلوبة
echo "<h3>2️⃣ إنشاء المجلدات:</h3>";
$directories = [
    '../storage/app/public',
    '../storage/framework/cache',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/logs',
    '../bootstrap/cache'
];

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

// 3. تعيين الصلاحيات
echo "<h3>3️⃣ تعيين الصلاحيات:</h3>";
$chmod_dirs = [
    '../storage' => 0775,
    '../bootstrap/cache' => 0775
];

foreach ($chmod_dirs as $dir => $permission) {
    if (chmod($dir, $permission)) {
        echo "✅ تم تعيين صلاحيات: $dir<br>";
    } else {
        echo "❌ فشل في تعيين صلاحيات: $dir<br>";
    }
}

// 4. اختبار Laravel
echo "<h3>4️⃣ اختبار Laravel:</h3>";
try {
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار بسيط
    $request = \Illuminate\Http\Request::create('/', 'GET');
    echo "✅ تم إنشاء HTTP Request<br>";
    
} catch (Exception $e) {
    echo "❌ خطأ في Laravel: " . $e->getMessage() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h2>✅ تم الانتهاء من الإصلاح</h2>";
echo "<p>جرب الروابط التالية الآن:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank'>الصفحة الرئيسية</a></li>";
echo "<li><a href='/login' target='_blank'>تسجيل الدخول</a></li>";
echo "<li><a href='/quick-test.php' target='_blank'>الاختبار السريع</a></li>";
echo "</ul>";
echo "</div>";

// 5. إنشاء ملف index.php بديل
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h2>🔧 إنشاء ملف index.php بديل</h2>";

$indexContent = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define("LARAVEL_START", microtime(true));

// Check for maintenance mode
if (file_exists($maintenance = __DIR__."/../storage/framework/maintenance.php")) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__."/../vendor/autoload.php";

// Bootstrap Laravel and handle the request
try {
    $app = require_once __DIR__."/../bootstrap/app.php";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    echo "<h1>خطأ في التطبيق</h1>";
    echo "<p>رسالة الخطأ: " . $e->getMessage() . "</p>";
    echo "<p>الملف: " . $e->getFile() . "</p>";
    echo "<p>السطر: " . $e->getLine() . "</p>";
    echo "<a href=\"/simple-fix.php\">إصلاح المشاكل</a>";
}
';

if (file_put_contents('../public/index-backup.php', $indexContent)) {
    echo "✅ تم إنشاء ملف index-backup.php<br>";
    echo "<p>يمكنك استخدام هذا الملف كبديل إذا لم يعمل index.php الأصلي</p>";
} else {
    echo "❌ فشل في إنشاء ملف index-backup.php<br>";
}

echo "</div>";

?>
