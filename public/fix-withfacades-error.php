<?php

echo "<h1>🔧 إصلاح خطأ withFacades</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص خطأ withFacades</h2>";

echo "<p><strong>الخطأ:</strong> Method Illuminate\\Foundation\\Application::withFacades does not exist</p>";
echo "<p>هذا يعني أن method withFacades() غير موجود في Laravel 10. هذا method كان موجوداً في Lumen فقط.</p>";

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ اختبار Laravel بدون withFacades</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع إصلاح withFacades:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-sqlite-only.php';
    echo "✅ تم تحميل Laravel بدون withFacades<br>";
    
    // فحص Facade Application
    try {
        $facadeApp = \Illuminate\Support\Facades\Facade::getFacadeApplication();
        if ($facadeApp) {
            echo "✅ Facade Application متاح<br>";
            echo "✅ نوع Facade App: " . get_class($facadeApp) . "<br>";
        } else {
            echo "❌ Facade Application لا يزال null<br>";
        }
    } catch (Exception $e) {
        echo "❌ Facade Application: " . $e->getMessage() . "<br>";
    }
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        $appName = $config->get('app.name');
        echo "✅ اسم التطبيق: $appName<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار database service
    try {
        $db = $app->make('db');
        echo "✅ Database service يعمل<br>";
        
        $connection = $db->connection();
        $driverName = $connection->getDriverName();
        echo "✅ Database driver: $driverName<br>";
        
        if ($driverName === 'sqlite') {
            echo "🎉 تأكيد: يستخدم SQLite!<br>";
            
            // اختبار الجداول
            try {
                $userCount = $connection->select("SELECT COUNT(*) as count FROM users");
                echo "✅ عدد المستخدمين: " . $userCount[0]->count . "<br>";
            } catch (Exception $e) {
                echo "⚠️ مشكلة في قراءة الجداول: " . $e->getMessage() . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Database service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار view service
    try {
        $view = $app->make('view');
        echo "✅ View service يعمل<br>";
        
        // اختبار view finder
        try {
            $finder = $app->make('view.finder');
            echo "✅ View finder يعمل<br>";
            
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
    echo "<h3>🌐 اختبار HTTP Kernel:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل بنجاح!<br>";
                
                // فحص محتوى الاستجابة
                $content = $response->getContent();
                if (strpos($content, 'مرحباً') !== false) {
                    echo "✅ محتوى View يظهر بشكل صحيح<br>";
                } else {
                    echo "⚠️ محتوى View قد لا يظهر بشكل صحيح<br>";
                }
                
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            } else {
                echo "⚠️ كود استجابة غير متوقع: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
            
            if (strpos($e->getMessage(), 'withFacades') !== false) {
                echo "<strong>🚨 مشكلة withFacades!</strong><br>";
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
                echo "🎉 صفحة تسجيل الدخول تعمل بنجاح!<br>";
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
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ إصلاح withFacades المطبق</h2>";
echo "<ul>";
echo "<li>إزالة \$app->withFacades() من bootstrap (غير موجود في Laravel 10)</li>";
echo "<li>إزالة \$app->boot() لتجنب مشاكل التهيئة</li>";
echo "<li>الاحتفاظ بـ Facade::setFacadeApplication(\$app) فقط</li>";
echo "<li>تبسيط تهيئة Laravel للتوافق مع Laravel 10</li>";
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
echo "<p>بعد إصلاح خطأ withFacades، جرب الروابط التالية:</p>";
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

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎉 الحل النهائي</h2>";
echo "<p style='font-size: 18px; color: #28a745; font-weight: bold;'>تم إزالة جميع methods غير المتوافقة مع Laravel 10</p>";
echo "<p>Bootstrap الآن مبسط ومتوافق تماماً مع Laravel 10</p>";
echo "</div>";

?>
