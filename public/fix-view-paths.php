<?php

echo "<h1>🔧 إصلاح مسارات View</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة View Paths</h2>";

echo "<p><strong>الخطأ:</strong> FileViewFinder::__construct(): Argument #2 (\$paths) must be of type array, null given</p>";
echo "<p>هذا يعني أن view paths لم يتم تعيينها بشكل صحيح في config.</p>";

// إنشاء مجلدات view إذا لم تكن موجودة
$viewDirs = [
    '../resources/views',
    '../storage/framework/views',
    '../storage/framework/cache/data',
    '../storage/framework/sessions'
];

echo "<h3>📂 إنشاء المجلدات المطلوبة:</h3>";
foreach ($viewDirs as $dir) {
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

// إنشاء ملف view تجريبي
$testViewPath = '../resources/views/welcome.blade.php';
if (!file_exists($testViewPath)) {
    $testViewContent = '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .container { background: rgba(255,255,255,0.1); padding: 40px; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 مرحباً بك في نظام إدارة الصيدلية</h1>
        <p>تم تحميل Laravel بنجاح مع SQLite!</p>
        <p>الوقت الحالي: {{ date("Y-m-d H:i:s") }}</p>
        <a href="/login" style="display: inline-block; padding: 15px 30px; background: #ff9800; color: white; text-decoration: none; border-radius: 8px; margin: 10px;">تسجيل الدخول</a>
    </div>
</body>
</html>';
    
    if (file_put_contents($testViewPath, $testViewContent)) {
        echo "✅ تم إنشاء ملف welcome.blade.php<br>";
    } else {
        echo "❌ فشل في إنشاء ملف welcome.blade.php<br>";
    }
} else {
    echo "✅ ملف welcome.blade.php موجود<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ اختبار Laravel مع View Paths محسنة</h2>";

try {
    echo "<h3>📁 تحميل Laravel:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-sqlite-only.php';
    echo "✅ تم تحميل Laravel مع bootstrap محسن<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        // فحص view paths
        $viewPaths = $config->get('view.paths');
        if (is_array($viewPaths) && !empty($viewPaths)) {
            echo "✅ View paths تم تعيينها:<br>";
            foreach ($viewPaths as $path) {
                echo "  - $path<br>";
                if (is_dir($path)) {
                    echo "    ✅ المجلد موجود<br>";
                } else {
                    echo "    ❌ المجلد غير موجود<br>";
                }
            }
        } else {
            echo "❌ View paths غير معينة أو فارغة<br>";
        }
        
        $compiledPath = $config->get('view.compiled');
        echo "✅ Compiled path: $compiledPath<br>";
        if (is_dir($compiledPath)) {
            echo "  ✅ مجلد compiled موجود<br>";
        } else {
            echo "  ❌ مجلد compiled غير موجود<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار view service
    try {
        $view = $app->make('view');
        echo "✅ View service يعمل<br>";
        echo "✅ نوع View: " . get_class($view) . "<br>";
        
        // اختبار view finder
        try {
            $finder = $app->make('view.finder');
            echo "✅ View finder يعمل<br>";
            echo "✅ نوع View Finder: " . get_class($finder) . "<br>";
            
            // اختبار البحث عن view
            if (file_exists('../resources/views/welcome.blade.php')) {
                try {
                    $welcomeViewPath = $finder->find('welcome');
                    echo "✅ العثور على welcome view: $welcomeViewPath<br>";
                } catch (Exception $e) {
                    echo "⚠️ لم يتم العثور على welcome view: " . $e->getMessage() . "<br>";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ View finder: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ View service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel مع Views:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request مع Views - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل مع Views!<br>";
                
                // فحص محتوى الاستجابة
                $content = $response->getContent();
                if (strpos($content, 'مرحباً بك') !== false) {
                    echo "✅ محتوى View يظهر بشكل صحيح<br>";
                }
                
            } elseif ($status == 302) {
                echo "🔄 إعادة توجيه (طبيعي)<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'view') !== false || strpos($e->getMessage(), 'FileViewFinder') !== false) {
                echo "<strong>⚠️ مشكلة في Views!</strong><br>";
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
echo "<h2>✅ إصلاح View Paths المطبق</h2>";
echo "<ul>";
echo "<li>تحديث view paths لاستخدام مسارات مطلقة بدلاً من helper functions</li>";
echo "<li>تحديث compiled path لاستخدام مسار مطلق</li>";
echo "<li>إنشاء جميع المجلدات المطلوبة للـ views</li>";
echo "<li>إنشاء ملف welcome.blade.php تجريبي</li>";
echo "<li>إصلاح جميع مسارات storage في bootstrap</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مسارات Views، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/fix-facades-error.php' => '🔧 إصلاح Facades'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
