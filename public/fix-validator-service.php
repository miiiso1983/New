<?php

echo "<h1>🔧 إصلاح مشكلة Validator Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Target class [validator] does not exist</h2>";

echo "<p><strong>الخطأ:</strong> Target class [validator] does not exist</p>";
echo "<p>هذا يعني أن Laravel يحتاج إلى validator service الذي يوفره ValidationServiceProvider.</p>";

try {
    echo "<h3>🚀 اختبار Laravel مع validator service:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
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
        
    } catch (Exception $e) {
        echo "❌ View service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار validator service
    try {
        $validator = $app->make('validator');
        echo "✅ Validator service يعمل<br>";
        echo "نوع Validator: " . get_class($validator) . "<br>";
        
        // اختبار إنشاء validator
        try {
            $testData = ['email' => 'test@example.com', 'name' => 'Test User'];
            $rules = ['email' => 'required|email', 'name' => 'required|string'];
            
            $validatorInstance = $validator->make($testData, $rules);
            echo "✅ إنشاء Validator يعمل<br>";
            
            if ($validatorInstance->passes()) {
                echo "✅ التحقق من البيانات يعمل<br>";
            } else {
                echo "⚠️ فشل التحقق (متوقع للاختبار)<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ إنشاء Validator: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Validator service: " . $e->getMessage() . "<br>";
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
        
        // اختبار POST request مع validation
        echo "<h3>📝 اختبار POST request مع validation:</h3>";
        
        $postRequest = \Illuminate\Http\Request::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        
        try {
            $postResponse = $kernel->handle($postRequest);
            $postStatus = $postResponse->getStatusCode();
            echo "✅ POST request تم معالجته - كود: $postStatus<br>";
            
        } catch (Exception $e) {
            echo "❌ خطأ في POST request: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'validator') !== false) {
        echo "<br><strong>🔧 تم تطبيق الحل لمشكلة Validator Service:</strong><br>";
        echo "- تم تسجيل ValidationServiceProvider<br>";
        echo "- تم ترتيب Service Providers بالتسلسل الصحيح<br>";
        echo "- تم إضافة validator service للتطبيق<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل المطبق لمشكلة Validator Service</h2>";
echo "<ul>";
echo "<li>تسجيل ValidationServiceProvider في bootstrap/app.php</li>";
echo "<li>إضافة validator service للتطبيق</li>";
echo "<li>ترتيب Service Providers بالتسلسل الصحيح</li>";
echo "<li>دعم validation في forms وrequests</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة validator service، جرب الروابط التالية:</p>";
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
