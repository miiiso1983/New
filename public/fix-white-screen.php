<?php

echo "<h1>إصلاح الشاشة البيضاء</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>الخطوة 1: فحص الملفات الأساسية</h2>";

$required_files = [
    '../vendor/autoload.php' => 'Composer Autoload',
    '../bootstrap/app.php' => 'Laravel Bootstrap',
    '../.env' => 'Environment File',
    '../database/database.sqlite' => 'Database File'
];

$all_files_exist = true;
foreach ($required_files as $file => $name) {
    if (file_exists($file)) {
        echo "✅ $name موجود<br>";
    } else {
        echo "❌ $name غير موجود<br>";
        $all_files_exist = false;
    }
}

if (!$all_files_exist) {
    echo "<h3>إنشاء الملفات المفقودة:</h3>";
    
    // إنشاء قاعدة البيانات
    if (!file_exists('../database/database.sqlite')) {
        if (touch('../database/database.sqlite')) {
            echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
        } else {
            echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
        }
    }
}

echo "<h2>الخطوة 2: فحص الصلاحيات</h2>";

$directories = [
    '../storage' => 'Storage Directory',
    '../bootstrap/cache' => 'Bootstrap Cache',
    '../storage/logs' => 'Logs Directory',
    '../storage/framework' => 'Framework Directory'
];

foreach ($directories as $dir => $name) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ $name قابل للكتابة<br>";
        } else {
            echo "⚠️ $name غير قابل للكتابة - محاولة إصلاح...<br>";
            if (chmod($dir, 0775)) {
                echo "✅ تم إصلاح صلاحيات $name<br>";
            } else {
                echo "❌ فشل في إصلاح صلاحيات $name<br>";
            }
        }
    } else {
        echo "⚠️ $name غير موجود - محاولة إنشاء...<br>";
        if (mkdir($dir, 0775, true)) {
            echo "✅ تم إنشاء $name<br>";
        } else {
            echo "❌ فشل في إنشاء $name<br>";
        }
    }
}

echo "<h2>الخطوة 3: تنظيف الكاش</h2>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    $commands = [
        'config:clear' => 'مسح كاش الإعدادات',
        'cache:clear' => 'مسح الكاش العام',
        'route:clear' => 'مسح كاش المسارات',
        'view:clear' => 'مسح كاش العروض'
    ];
    
    foreach ($commands as $command => $description) {
        try {
            $kernel->call($command);
            echo "✅ $description<br>";
        } catch (Exception $e) {
            echo "⚠️ فشل في $description: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في تحميل Laravel: " . $e->getMessage() . "<br>";
}

echo "<h2>الخطوة 4: فحص إعدادات .env</h2>";

if (file_exists('../.env')) {
    $env_content = file_get_contents('../.env');
    
    $required_settings = [
        'APP_KEY=' => 'مفتاح التطبيق',
        'APP_ENV=production' => 'بيئة الإنتاج',
        'DB_CONNECTION=sqlite' => 'نوع قاعدة البيانات'
    ];
    
    foreach ($required_settings as $setting => $description) {
        if (strpos($env_content, $setting) !== false) {
            echo "✅ $description محدد بشكل صحيح<br>";
        } else {
            echo "⚠️ $description غير محدد أو خاطئ<br>";
        }
    }
} else {
    echo "❌ ملف .env غير موجود<br>";
}

echo "<h2>الخطوة 5: اختبار Laravel</h2>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    echo "✅ تم تحميل Laravel بنجاح<br>";
    echo "إصدار Laravel: " . $app->version() . "<br>";
    
    // اختبار قاعدة البيانات
    try {
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('migrate:status');
        echo "✅ قاعدة البيانات تعمل<br>";
    } catch (Exception $e) {
        echo "⚠️ مشكلة في قاعدة البيانات: " . $e->getMessage() . "<br>";
        echo "محاولة تشغيل المايجريشن...<br>";
        try {
            $kernel->call('migrate --force');
            echo "✅ تم تشغيل المايجريشن<br>";
        } catch (Exception $e2) {
            echo "❌ فشل في تشغيل المايجريشن: " . $e2->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "<h2>النتيجة النهائية</h2>";
echo "<p>بعد تشغيل هذا السكريبت، جرب الروابط التالية:</p>";
echo "<a href='/' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>الصفحة الرئيسية</a>";
echo "<a href='/login' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>تسجيل الدخول</a>";
echo "<a href='/debug.php' target='_blank' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>التشخيص</a>";

?>
