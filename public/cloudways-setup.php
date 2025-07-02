<?php

echo "<h1>إعداد Cloudways للنظام</h1>";

// إنشاء المجلدات المطلوبة
echo "<h2>إنشاء المجلدات:</h2>";
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
            echo "✅ تم إنشاء المجلد: $dir<br>";
        } else {
            echo "❌ فشل في إنشاء المجلد: $dir<br>";
        }
    } else {
        echo "✅ المجلد موجود: $dir<br>";
    }
}

// تعيين الصلاحيات
echo "<h2>تعيين الصلاحيات:</h2>";
$chmod_dirs = [
    '../storage' => 0775,
    '../bootstrap/cache' => 0775
];

foreach ($chmod_dirs as $dir => $permission) {
    if (chmod($dir, $permission)) {
        echo "✅ تم تعيين صلاحيات $dir<br>";
    } else {
        echo "❌ فشل في تعيين صلاحيات $dir<br>";
    }
}

// إنشاء قاعدة البيانات SQLite
echo "<h2>إعداد قاعدة البيانات:</h2>";
$db_file = '../database/database.sqlite';
if (!file_exists($db_file)) {
    if (touch($db_file)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
        chmod($db_file, 0664);
    } else {
        echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
    }
} else {
    echo "✅ ملف قاعدة البيانات موجود<br>";
}

// تشغيل أوامر Laravel
echo "<h2>تشغيل أوامر Laravel:</h2>";

try {
    // تحميل Laravel
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    // تشغيل الأوامر
    $commands = [
        'config:clear',
        'cache:clear',
        'route:clear',
        'view:clear',
        'migrate --force',
        'db:seed --force'
    ];
    
    foreach ($commands as $command) {
        try {
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->call($command);
            echo "✅ تم تشغيل الأمر: $command<br>";
        } catch (Exception $e) {
            echo "❌ فشل في تشغيل الأمر $command: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في تحميل Laravel: " . $e->getMessage() . "<br>";
}

echo "<h2>اختبار الموقع:</h2>";
echo "<a href='/' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>اختبار الموقع الآن</a><br><br>";

echo "<h2>روابط مفيدة:</h2>";
echo "<a href='/login' target='_blank'>صفحة تسجيل الدخول</a><br>";
echo "<a href='/debug.php' target='_blank'>صفحة التشخيص</a><br>";

?>
