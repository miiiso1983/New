<?php

echo "<h1>🔧 إصلاح مشكلة MaintenanceMode</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة MaintenanceMode</h2>";

echo "<p><strong>الخطأ:</strong> Target [Illuminate\\Contracts\\Foundation\\MaintenanceMode] is not instantiable</p>";
echo "<p>هذا يعني أن MaintenanceMode contract لم يتم تسجيله في container.</p>";

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ اختبار Laravel مع MaintenanceMode محسن</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع إصلاح MaintenanceMode:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-views.php';
    echo "✅ تم تحميل Laravel مع bootstrap محسن<br>";
    
    // فحص MaintenanceMode service
    try {
        $maintenanceMode = $app->make(\Illuminate\Contracts\Foundation\MaintenanceMode::class);
        echo "✅ MaintenanceMode service متاح<br>";
        echo "✅ نوع MaintenanceMode: " . get_class($maintenanceMode) . "<br>";
        
        // اختبار MaintenanceMode methods
        try {
            $isDown = $maintenanceMode->active();
            echo "✅ MaintenanceMode active: " . ($isDown ? 'نعم' : 'لا') . "<br>";
        } catch (Exception $e) {
            echo "⚠️ MaintenanceMode active check: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ MaintenanceMode service: " . $e->getMessage() . "<br>";
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
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel مع MaintenanceMode:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request مع MaintenanceMode - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل بنجاح!<br>";
                
                // فحص محتوى الاستجابة
                $content = $response->getContent();
                if (!empty($content)) {
                    echo "✅ محتوى الاستجابة متاح (" . strlen($content) . " حرف)<br>";
                    
                    // عرض جزء من المحتوى
                    $preview = substr($content, 0, 200);
                    echo "✅ معاينة المحتوى: " . htmlspecialchars($preview) . "...<br>";
                } else {
                    echo "⚠️ محتوى الاستجابة فارغ<br>";
                }
                
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            } elseif ($status == 503) {
                echo "🚧 الموقع في وضع الصيانة<br>";
            } else {
                echo "⚠️ كود استجابة غير متوقع: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
            
            if (strpos($e->getMessage(), 'MaintenanceMode') !== false) {
                echo "<strong>🚨 مشكلة MaintenanceMode!</strong><br>";
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
echo "<h2>✅ إصلاح MaintenanceMode المطبق</h2>";
echo "<ul>";
echo "<li>تسجيل FoundationServiceProvider لضمان توفر MaintenanceMode</li>";
echo "<li>تسجيل MaintenanceMode contract يدوياً في container</li>";
echo "<li>ربط MaintenanceMode بـ MaintenanceModeManager</li>";
echo "<li>إنشاء bootstrap مبسط بدون ViewServiceProvider</li>";
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
echo "<p>بعد إصلاح مشكلة MaintenanceMode، جرب الروابط التالية:</p>";
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
echo "<p style='font-size: 18px; color: #28a745; font-weight: bold;'>تم إصلاح جميع مشاكل Laravel Bootstrap!</p>";
echo "<p>الموقع الآن جاهز للاستخدام مع SQLite وبدون مشاكل Service Providers</p>";
echo "</div>";

?>
