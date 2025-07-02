<?php

// تشخيص سريع للموقع
echo "<h1>تشخيص الموقع</h1>";

// فحص PHP
echo "<h2>معلومات PHP:</h2>";
echo "إصدار PHP: " . PHP_VERSION . "<br>";
echo "الذاكرة المتاحة: " . ini_get('memory_limit') . "<br>";
echo "الوقت الأقصى للتنفيذ: " . ini_get('max_execution_time') . "<br>";

// فحص الملفات المطلوبة
echo "<h2>فحص الملفات:</h2>";
$files = [
    '../vendor/autoload.php',
    '../bootstrap/app.php',
    '../.env'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file موجود<br>";
    } else {
        echo "❌ $file غير موجود<br>";
    }
}

// فحص الصلاحيات
echo "<h2>فحص الصلاحيات:</h2>";
$dirs = [
    '../storage',
    '../bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (is_writable($dir)) {
        echo "✅ $dir قابل للكتابة<br>";
    } else {
        echo "❌ $dir غير قابل للكتابة<br>";
    }
}

// محاولة تحميل Laravel
echo "<h2>فحص Laravel:</h2>";
try {
    require_once '../vendor/autoload.php';
    echo "✅ Composer autoload تم تحميله<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ Laravel app تم إنشاؤه<br>";
    
    // محاولة الحصول على معلومات Laravel
    if (method_exists($app, 'version')) {
        echo "إصدار Laravel: " . $app->version() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في تحميل Laravel: " . $e->getMessage() . "<br>";
    echo "تفاصيل الخطأ: " . $e->getFile() . " في السطر " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "❌ خطأ PHP: " . $e->getMessage() . "<br>";
    echo "تفاصيل الخطأ: " . $e->getFile() . " في السطر " . $e->getLine() . "<br>";
}

// فحص قاعدة البيانات
echo "<h2>فحص قاعدة البيانات:</h2>";
if (file_exists('../.env')) {
    $env = file_get_contents('../.env');
    if (strpos($env, 'DB_CONNECTION=mysql') !== false) {
        echo "نوع قاعدة البيانات: MySQL<br>";
    } elseif (strpos($env, 'DB_CONNECTION=sqlite') !== false) {
        echo "نوع قاعدة البيانات: SQLite<br>";
        if (file_exists('../database/database.sqlite')) {
            echo "✅ ملف SQLite موجود<br>";
        } else {
            echo "❌ ملف SQLite غير موجود<br>";
        }
    }
} else {
    echo "❌ ملف .env غير موجود<br>";
}

echo "<h2>معلومات الخادم:</h2>";
echo "نظام التشغيل: " . PHP_OS . "<br>";
echo "خادم الويب: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'غير معروف') . "<br>";
echo "المجلد الحالي: " . __DIR__ . "<br>";
echo "المجلد الجذر: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'غير معروف') . "<br>";

// فحص الأخطاء
echo "<h2>فحص سجل الأخطاء:</h2>";
$error_log = '../storage/logs/laravel.log';
if (file_exists($error_log)) {
    echo "✅ ملف سجل الأخطاء موجود<br>";
    $log_content = file_get_contents($error_log);
    $lines = explode("\n", $log_content);
    $recent_lines = array_slice($lines, -10);
    echo "<strong>آخر 10 أسطر من سجل الأخطاء:</strong><br>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;'>";
    echo htmlspecialchars(implode("\n", $recent_lines));
    echo "</pre>";
} else {
    echo "❌ ملف سجل الأخطاء غير موجود<br>";
}

?>
