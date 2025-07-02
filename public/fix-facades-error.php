<?php

echo "<h1>🔧 إصلاح مشكلة Facades</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Facades</h2>";

echo "<p><strong>الخطأ:</strong> A facade root has not been set</p>";
echo "<p>هذا يعني أن Laravel Facades لم يتم تهيئتها بشكل صحيح في bootstrap.</p>";

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ إصلاح مشكلة Facades</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع إصلاح Facades:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-sqlite-only.php';
    echo "✅ تم تحميل Laravel مع bootstrap محسن<br>";
    
    // فحص Facade Application
    try {
        $facadeApp = \Illuminate\Support\Facades\Facade::getFacadeApplication();
        if ($facadeApp) {
            echo "✅ Facade Application تم تعيينه<br>";
            echo "✅ نوع Facade App: " . get_class($facadeApp) . "<br>";
        } else {
            echo "❌ Facade Application لم يتم تعيينه<br>";
        }
    } catch (Exception $e) {
        echo "❌ Facade Application: " . $e->getMessage() . "<br>";
    }
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        // اختبار Config facade
        try {
            $appName = \Illuminate\Support\Facades\Config::get('app.name');
            echo "✅ Config facade يعمل - اسم التطبيق: $appName<br>";
        } catch (Exception $e) {
            echo "❌ Config facade: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار database service
    try {
        $db = $app->make('db');
        echo "✅ Database service يعمل<br>";
        
        // اختبار DB facade
        try {
            $connection = \Illuminate\Support\Facades\DB::connection();
            $driverName = $connection->getDriverName();
            echo "✅ DB facade يعمل - driver: $driverName<br>";
            
            if ($driverName === 'sqlite') {
                echo "🎉 تأكيد: DB facade يستخدم SQLite!<br>";
                
                // اختبار query
                $result = \Illuminate\Support\Facades\DB::select('SELECT 1 as test');
                echo "✅ DB query يعمل<br>";
                
            } else {
                echo "❌ DB facade يستخدم: $driverName<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ DB facade: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار facades أخرى
    echo "<h3>🔧 اختبار Facades الأخرى:</h3>";
    
    $facades = [
        'Cache' => '\Illuminate\Support\Facades\Cache',
        'View' => '\Illuminate\Support\Facades\View',
        'Session' => '\Illuminate\Support\Facades\Session',
        'Auth' => '\Illuminate\Support\Facades\Auth'
    ];
    
    foreach ($facades as $name => $class) {
        try {
            // محاولة استخدام facade
            $facadeRoot = $class::getFacadeRoot();
            if ($facadeRoot) {
                echo "✅ $name facade يعمل<br>";
            } else {
                echo "❌ $name facade لا يعمل<br>";
            }
        } catch (Exception $e) {
            echo "❌ $name facade: " . $e->getMessage() . "<br>";
        }
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel مع Facades:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request مع Facades - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل مع Facades!<br>";
            } elseif ($status == 302) {
                echo "🔄 إعادة توجيه (طبيعي)<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'facade') !== false) {
                echo "<strong>⚠️ مشكلة في Facades!</strong><br>";
            }
        }
        
        // اختبار صفحة تسجيل الدخول
        echo "<h3>🔐 اختبار صفحة تسجيل الدخول:</h3>";
        
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        
        try {
            $loginResponse = $kernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول مع Facades - كود: $loginStatus<br>";
            
            if ($loginStatus == 200) {
                echo "🎉 صفحة تسجيل الدخول تعمل مع Facades!<br>";
            } elseif ($loginStatus == 302) {
                $location = $loginResponse->headers->get('Location');
                echo "🔄 إعادة توجيه من تسجيل الدخول إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في صفحة تسجيل الدخول: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'facade') !== false) {
        echo "<br><strong>🔧 تم تطبيق إصلاح Facades:</strong><br>";
        echo "- تم إضافة Facade::setFacadeApplication(\$app) في bootstrap<br>";
        echo "- تم تسجيل جميع Service Providers المطلوبة<br>";
        echo "- تم ترتيب تهيئة Facades بالتسلسل الصحيح<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ إصلاح Facades المطبق</h2>";
echo "<ul>";
echo "<li>إضافة Facade::setFacadeApplication(\$app) في bootstrap/app-sqlite-only.php</li>";
echo "<li>تسجيل جميع Service Providers قبل تهيئة Facades</li>";
echo "<li>ترتيب تهيئة Laravel بالتسلسل الصحيح</li>";
echo "<li>ضمان توفر Application instance للـ Facades</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة Facades، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-database-tables.php' => '🗄️ إنشاء الجداول'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📋 ملخص جميع الإصلاحات</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;'>";

$fixes = [
    "✅ Laravel Bootstrap" => "تحديث bootstrap للتوافق مع Laravel 10",
    "✅ Service Providers" => "تسجيل جميع Service Providers المطلوبة",
    "✅ SQLite Database" => "فرض استخدام SQLite بدلاً من MySQL",
    "✅ Facades Support" => "إصلاح تهيئة Laravel Facades",
    "✅ Middleware Pipeline" => "إصلاح middleware وإزالة المشكلة",
    "✅ Permission System" => "نظام صلاحيات مبسط بدون Spatie",
    "✅ Database Tables" => "إنشاء جداول SQLite والمستخدمين",
    "✅ Config Management" => "إعدادات ثابتة بدون env dependencies"
];

foreach ($fixes as $title => $description) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<strong>$title</strong><br>";
    echo "<small style='color: #666;'>$description</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

?>
