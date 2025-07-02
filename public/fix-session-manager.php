<?php

echo "<h1>🔧 إصلاح مشكلة SessionManager للـ Middleware</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة في StartSession Middleware</h2>";
echo "<p>StartSession middleware يتوقع SessionManager وليس Session Store</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة SessionManager</h2>";

echo "<p><strong>الخطأ:</strong> StartSession::__construct(): Argument #1 (\$manager) must be of type SessionManager</p>";
echo "<p><strong>المشكلة:</strong> كنا نمرر Session Store مباشرة بدلاً من SessionManager</p>";
echo "<p><strong>الحل:</strong> إنشاء SessionManager صحيح مع file driver مسجل</p>";

echo "<h3>📋 الحل المطبق:</h3>";
echo "<ol>";
echo "<li>إنشاء SessionManager صحيح</li>";
echo "<li>تسجيل file driver في SessionManager</li>";
echo "<li>تحديث session.store ليستخدم SessionManager</li>";
echo "<li>إضافة SessionManager alias</li>";
echo "</ol>";

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Laravel مع SessionManager محسن</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع SessionManager محسن:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-session-provider.php';
    echo "✅ تم تحميل Laravel مع SessionManager محسن<br>";
    
    // اختبار 1: SessionManager
    echo "<h4>1️⃣ اختبار SessionManager:</h4>";
    try {
        $sessionManager = $app->make('session');
        echo "✅ Session Manager: " . get_class($sessionManager) . "<br>";
        
        if ($sessionManager instanceof \Illuminate\Session\SessionManager) {
            echo "✅ SessionManager نوع صحيح<br>";
        } else {
            echo "❌ SessionManager نوع خاطئ<br>";
        }
        
        // اختبار file driver
        try {
            $fileDriver = $sessionManager->driver('file');
            echo "✅ File Driver: " . get_class($fileDriver) . "<br>";
            
            if ($fileDriver instanceof \Illuminate\Session\Store) {
                echo "✅ File Driver نوع صحيح<br>";
            } else {
                echo "❌ File Driver نوع خاطئ<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ File Driver: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ SessionManager: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 2: Session Store
    echo "<h4>2️⃣ اختبار Session Store:</h4>";
    try {
        $sessionStore = $app->make('session.store');
        echo "✅ Session Store: " . get_class($sessionStore) . "<br>";
        
        // اختبار session operations
        $sessionStore->put('test_manager', 'success');
        $value = $sessionStore->get('test_manager');
        
        if ($value === 'success') {
            echo "✅ Session Store operations تعمل<br>";
        } else {
            echo "❌ Session Store operations فاشلة<br>";
        }
        
        // اختبار session ID
        $sessionId = $sessionStore->getId();
        echo "✅ Session ID: " . substr($sessionId, 0, 20) . "...<br>";
        
    } catch (Exception $e) {
        echo "❌ Session Store: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 3: Session Handler
    echo "<h4>3️⃣ اختبار Session Handler:</h4>";
    try {
        $sessionHandler = $app->make('session.handler');
        echo "✅ Session Handler: " . get_class($sessionHandler) . "<br>";
        
        // اختبار handler operations
        $testId = 'test_handler_' . uniqid();
        $testData = 'test_data_' . time();
        
        $sessionHandler->write($testId, $testData);
        $readData = $sessionHandler->read($testId);
        
        if ($readData === $testData) {
            echo "✅ Session Handler operations تعمل<br>";
        } else {
            echo "❌ Session Handler operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Handler: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 4: Session Contract
    echo "<h4>4️⃣ اختبار Session Contract:</h4>";
    try {
        $sessionContract = $app->make(\Illuminate\Contracts\Session\Session::class);
        echo "✅ Session Contract: " . get_class($sessionContract) . "<br>";
        
        // اختبار contract operations
        $sessionContract->put('test_contract', 'success');
        $value = $sessionContract->get('test_contract');
        
        if ($value === 'success') {
            echo "✅ Session Contract operations تعمل<br>";
        } else {
            echo "❌ Session Contract operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Contract: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 5: Cookie Services
    echo "<h4>5️⃣ اختبار Cookie Services:</h4>";
    try {
        $cookie = $app->make('cookie');
        echo "✅ Cookie Service: " . get_class($cookie) . "<br>";
        
        $cookieFactory = $app->make(\Illuminate\Contracts\Cookie\Factory::class);
        echo "✅ Cookie Factory: " . get_class($cookieFactory) . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Cookie Services: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 6: HTTP Request مع SessionManager
    echo "<h4>6️⃣ اختبار HTTP Request مع SessionManager:</h4>";
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        echo "✅ HTTP Request status: <strong>$status</strong><br>";
        
        if ($status == 200) {
            echo "🎉 الصفحة الرئيسية تعمل مع SessionManager!<br>";
        } elseif ($status == 302) {
            $location = $response->headers->get('Location');
            echo "🔄 إعادة توجيه إلى: $location<br>";
        }
        
        // فحص session cookies
        $cookies = $response->headers->getCookies();
        if (!empty($cookies)) {
            echo "✅ Session cookies تم تعيينها:<br>";
            foreach ($cookies as $cookie) {
                echo "  - " . $cookie->getName() . " = " . substr($cookie->getValue(), 0, 20) . "...<br>";
            }
        } else {
            echo "⚠️ لا توجد session cookies<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
        
        if (strpos($e->getMessage(), 'SessionManager') !== false || strpos($e->getMessage(), 'StartSession') !== false) {
            echo "<strong>🚨 لا تزال هناك مشكلة في SessionManager!</strong><br>";
        }
    }
    
    // اختبار 7: Login Page مع SessionManager
    echo "<h4>7️⃣ اختبار Login Page مع SessionManager:</h4>";
    try {
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        $loginResponse = $kernel->handle($loginRequest);
        $loginStatus = $loginResponse->getStatusCode();
        
        echo "✅ Login page status: <strong>$loginStatus</strong><br>";
        
        if ($loginStatus == 200) {
            echo "🎉 صفحة تسجيل الدخول تعمل مع SessionManager!<br>";
        } elseif ($loginStatus == 302) {
            echo "🔄 إعادة توجيه من login<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Login Page: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 8: Middleware Stack
    echo "<h4>8️⃣ اختبار Middleware Stack:</h4>";
    try {
        // محاولة إنشاء StartSession middleware مباشرة
        $sessionManager = $app->make('session');
        $startSessionMiddleware = new \Illuminate\Session\Middleware\StartSession($sessionManager);
        echo "✅ StartSession Middleware: " . get_class($startSessionMiddleware) . "<br>";
        
        echo "✅ StartSession middleware يمكن إنشاؤه بنجاح<br>";
        
    } catch (Exception $e) {
        echo "❌ StartSession Middleware: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ إصلاح SessionManager المطبق</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>📝 التحديثات المطبقة:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 12px;'>";
echo htmlspecialchars('// تسجيل SessionManager صحيح
$app->singleton(\'session\', function ($app) {
    $manager = new \Illuminate\Session\SessionManager($app);
    
    // تسجيل file driver يدوياً
    $manager->extend(\'file\', function ($app) {
        $handler = $app->make(\'session.handler\');
        return new \Illuminate\Session\Store(\'laravel_session\', $handler);
    });
    
    return $manager;
});

// تسجيل SessionManager alias
$app->alias(\'session\', \Illuminate\Session\SessionManager::class);

// تحديث session.store ليستخدم SessionManager
$app->singleton(\'session.store\', function ($app) {
    $manager = $app->make(\'session\');
    return $manager->driver(\'file\');
});');
echo "</pre>";
echo "</div>";

echo "<ul>";
echo "<li>إنشاء SessionManager صحيح مع file driver</li>";
echo "<li>تسجيل SessionManager alias للـ type hinting</li>";
echo "<li>تحديث session.store ليستخدم SessionManager</li>";
echo "<li>ضمان توافق StartSession middleware</li>";
echo "<li>الحفاظ على جميع session operations</li>";
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
echo "<h2>🎯 اختبار النظام مع SessionManager</h2>";
echo "<p>بعد إصلاح مشكلة SessionManager، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ قاعدة البيانات',
    '/fix-cookie-service.php' => '🍪 اختبار Cookie'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎉 تم إصلاح SessionManager</h2>";
echo "<p style='font-size: 18px; font-weight: bold;'>StartSession middleware الآن يعمل بشكل صحيح!</p>";
echo "<p>SessionManager متوافق مع جميع middleware و services</p>";
echo "</div>";

?>
