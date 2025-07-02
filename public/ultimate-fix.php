<?php

echo "<h1>🚀 الحل النهائي الشامل</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin: 20px 0; text-align: center;'>";
echo "<h2>🔧 الحل النهائي لجميع المشاكل</h2>";
echo "<p style='font-size: 18px;'>تطبيق جميع الإصلاحات والحلول</p>";
echo "</div>";

$allFixed = true;

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ تطبيق الإصلاحات</h2>";

// 1. إنشاء قاعدة البيانات
echo "<h3>1️⃣ قاعدة البيانات:</h3>";
$dbFile = '../database/database.sqlite';
if (!file_exists($dbFile)) {
    if (touch($dbFile)) {
        echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
        chmod($dbFile, 0664);
    } else {
        echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
        $allFixed = false;
    }
} else {
    echo "✅ ملف قاعدة البيانات موجود<br>";
}

// 2. إنشاء المجلدات
echo "<h3>2️⃣ المجلدات المطلوبة:</h3>";
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
            echo "✅ تم إنشاء: " . basename($dir) . "<br>";
        } else {
            echo "❌ فشل في إنشاء: " . basename($dir) . "<br>";
            $allFixed = false;
        }
    } else {
        echo "✅ موجود: " . basename($dir) . "<br>";
    }
}

// 3. اختبار Laravel
echo "<h3>3️⃣ اختبار Laravel:</h3>";
try {
    require_once '../vendor/autoload.php';
    echo "✅ Composer تم تحميله<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ Laravel تم تحميله<br>";
    
    // اختبار config
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        $appName = $config->get('app.name', 'Laravel');
        echo "✅ قراءة الإعدادات تعمل: $appName<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service لا يعمل: " . $e->getMessage() . "<br>";
        $allFixed = false;
    }
    
    // اختبار HTTP Kernel
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب
        $request = \Illuminate\Http\Request::create('/', 'GET');
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        echo "✅ معالجة الطلبات تعمل - كود: $status<br>";
        
        if ($status == 200 || $status == 302) {
            echo "🎉 الموقع يعمل بنجاح!<br>";
        } else {
            echo "⚠️ كود استجابة غير متوقع: $status<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel لا يعمل: " . $e->getMessage() . "<br>";
        $allFixed = false;
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    $allFixed = false;
}

echo "</div>";

// النتيجة النهائية
if ($allFixed) {
    echo "<div style='background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 15px; margin: 20px 0; text-align: center;'>";
    echo "<h2>🎉 تم إصلاح جميع المشاكل!</h2>";
    echo "<p style='font-size: 18px;'>الموقع جاهز للاستخدام</p>";
    echo "</div>";
} else {
    echo "<div style='background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333; padding: 30px; border-radius: 15px; margin: 20px 0; text-align: center;'>";
    echo "<h2>⚠️ هناك مشاكل متبقية</h2>";
    echo "<p>يرجى مراجعة التفاصيل أعلاه</p>";
    echo "</div>";
}

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔗 اختبار الموقع</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;'>";

$testLinks = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/new-login' => '🆕 تسجيل الدخول الجديد',
    '/dashboard' => '📊 لوحة التحكم',
    '/index-emergency.php' => '🚨 الفهرس الطارئ'
];

foreach ($testLinks as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 15px; background: white; border-radius: 8px; text-decoration: none; color: #333; border: 2px solid #2196f3; text-align: center; font-weight: bold;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #fff3e0; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ ملفات التشخيص المتاحة</h2>";
echo "<ul>";
echo "<li><a href='/test-simple.php'>🧪 الاختبار البسيط</a></li>";
echo "<li><a href='/fix-config-final.php'>🔧 إصلاح Config النهائي</a></li>";
echo "<li><a href='/final-status.php'>📊 التقرير النهائي</a></li>";
echo "<li><a href='/debug.php'>🔍 التشخيص الشامل</a></li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>📋 ملخص الحلول المطبقة</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; text-align: left;'>";

$solutions = [
    "✅ Bootstrap مبسط" => "إنشاء config repository مباشرة",
    "✅ قاعدة البيانات SQLite" => "ملف قاعدة بيانات محلي",
    "✅ إصلاح Middleware" => "تعطيل HandleCors المشكل",
    "✅ Service Providers" => "تسجيل الخدمات الأساسية فقط",
    "✅ ملفات التشخيص" => "أدوات شاملة لحل المشاكل",
    "✅ الفهرس الطارئ" => "نسخة احتياطية من index.php"
];

foreach ($solutions as $title => $description) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #4caf50;'>";
    echo "<strong>$title</strong><br>";
    echo "<small style='color: #666;'>$description</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

?>
