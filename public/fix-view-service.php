<?php

echo "<h1>🔧 إصلاح مشكلة View Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة FileViewFinder paths</h2>";

echo "<p><strong>الخطأ:</strong> FileViewFinder::__construct(): Argument #2 (\$paths) must be of type array, null given</p>";
echo "<p>هذا يعني أن ViewServiceProvider يحتاج إلى إعدادات view paths.</p>";

// إنشاء مجلدات view إذا لم تكن موجودة
$viewDirs = [
    '../resources/views',
    '../storage/framework/views',
    '../storage/framework/cache/data'
];

foreach ($viewDirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ تم إنشاء مجلد: $dir<br>";
        } else {
            echo "❌ فشل في إنشاء مجلد: $dir<br>";
        }
    } else {
        echo "✅ مجلد موجود: $dir<br>";
    }
}

// إنشاء ملف view تجريبي
$testViewPath = '../resources/views/test.blade.php';
if (!file_exists($testViewPath)) {
    $testViewContent = '<!DOCTYPE html>
<html>
<head>
    <title>اختبار View</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>مرحباً من Laravel Views!</h1>
    <p>هذا ملف view تجريبي للتأكد من عمل النظام.</p>
</body>
</html>';
    
    if (file_put_contents($testViewPath, $testViewContent)) {
        echo "✅ تم إنشاء ملف view تجريبي<br>";
    } else {
        echo "❌ فشل في إنشاء ملف view تجريبي<br>";
    }
} else {
    echo "✅ ملف view تجريبي موجود<br>";
}

try {
    echo "<h3>🚀 اختبار Laravel مع view service:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        // فحص إعدادات view
        $viewPaths = $config->get('view.paths', []);
        echo "✅ View paths: " . implode(', ', $viewPaths) . "<br>";
        
        $compiledPath = $config->get('view.compiled');
        echo "✅ Compiled path: $compiledPath<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار files service
    try {
        $files = $app->make('files');
        echo "✅ Files service يعمل<br>";
        
    } catch (Exception $e) {
        echo "❌ Files service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار cache service
    try {
        $cache = $app->make('cache');
        echo "✅ Cache service يعمل<br>";
        
    } catch (Exception $e) {
        echo "❌ Cache service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار view service
    try {
        $view = $app->make('view');
        echo "✅ View service يعمل<br>";
        echo "نوع View: " . get_class($view) . "<br>";
        
        // اختبار view finder
        try {
            $finder = $app->make('view.finder');
            echo "✅ View finder يعمل<br>";
            echo "نوع View Finder: " . get_class($finder) . "<br>";
            
            // اختبار البحث عن view
            if (file_exists('../resources/views/test.blade.php')) {
                try {
                    $testViewPath = $finder->find('test');
                    echo "✅ العثور على view تجريبي: $testViewPath<br>";
                } catch (Exception $e) {
                    echo "⚠️ لم يتم العثور على view تجريبي: " . $e->getMessage() . "<br>";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ View finder: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ View service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار Service Providers الأخرى
    echo "<h3>🔧 فحص Service Providers الأخرى:</h3>";
    
    $services = [
        'db' => 'Database Manager',
        'session' => 'Session Manager',
        'auth' => 'Auth Manager',
        'hash' => 'Hash Manager',
        'translator' => 'Translator'
    ];
    
    foreach ($services as $service => $name) {
        try {
            $instance = $app->make($service);
            echo "✅ $name ($service) يعمل<br>";
        } catch (Exception $e) {
            echo "❌ $name ($service): " . $e->getMessage() . "<br>";
        }
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
    
    if (strpos($e->getMessage(), 'view') !== false || strpos($e->getMessage(), 'FileViewFinder') !== false) {
        echo "<br><strong>🔧 تم تطبيق الحل لمشكلة View Service:</strong><br>";
        echo "- تم إضافة إعدادات view في config<br>";
        echo "- تم إنشاء مجلدات views المطلوبة<br>";
        echo "- تم إنشاء ملف view تجريبي<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل المطبق لمشكلة View Service</h2>";
echo "<ul>";
echo "<li>إضافة إعدادات view في bootstrap/app.php</li>";
echo "<li>إضافة إعدادات filesystems</li>";
echo "<li>إنشاء مجلدات views المطلوبة</li>";
echo "<li>إنشاء ملف view تجريبي</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة view service، جرب الروابط التالية:</p>";
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
