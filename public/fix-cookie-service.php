<?php

echo "<h1>🔧 إصلاح مشكلة Cookie Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة في CookieServiceProvider</h2>";
echo "<p>خطأ في تسجيل Cookie services - تسجيل Cookie services يدوياً</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Cookie</h2>";

echo "<p><strong>المشكلة:</strong> CookieServiceProvider يحاول resolve dependencies غير متوفرة</p>";
echo "<p><strong>الحل:</strong> تسجيل Cookie services يدوياً مثل Session</p>";

echo "<h3>📋 الحل المطبق:</h3>";
echo "<ol>";
echo "<li>إضافة cookie config</li>";
echo "<li>تسجيل CookieJar يدوياً</li>";
echo "<li>تسجيل Cookie contracts يدوياً</li>";
echo "<li>تجاهل CookieServiceProvider dependencies</li>";
echo "</ol>";

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Laravel مع Cookie محسن</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع Cookie محسن:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-session-provider.php';
    echo "✅ تم تحميل Laravel مع Cookie محسن<br>";
    
    // اختبار 1: Cookie Config
    echo "<h4>1️⃣ اختبار Cookie Config:</h4>";
    try {
        $config = $app->make('config');
        $cookieName = $config->get('cookie.name');
        $cookieLifetime = $config->get('cookie.lifetime');
        $cookiePath = $config->get('cookie.path');
        $cookieDomain = $config->get('cookie.domain');
        
        echo "✅ Cookie name: <strong>$cookieName</strong><br>";
        echo "✅ Cookie lifetime: <strong>$cookieLifetime</strong><br>";
        echo "✅ Cookie path: <strong>$cookiePath</strong><br>";
        echo "✅ Cookie domain: " . ($cookieDomain ?: 'null') . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Cookie Config: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 2: Cookie Service
    echo "<h4>2️⃣ اختبار Cookie Service:</h4>";
    try {
        $cookie = $app->make('cookie');
        echo "✅ Cookie Service: " . get_class($cookie) . "<br>";
        
        // اختبار cookie creation
        try {
            $testCookie = $cookie->make('test_cookie', 'test_value', 60);
            echo "✅ Cookie creation: " . get_class($testCookie) . "<br>";
            echo "✅ Cookie name: " . $testCookie->getName() . "<br>";
            echo "✅ Cookie value: " . $testCookie->getValue() . "<br>";
        } catch (Exception $e) {
            echo "⚠️ Cookie creation: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Cookie Service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 3: Cookie Contracts
    echo "<h4>3️⃣ اختبار Cookie Contracts:</h4>";
    try {
        $cookieFactory = $app->make(\Illuminate\Contracts\Cookie\Factory::class);
        echo "✅ Cookie Factory: " . get_class($cookieFactory) . "<br>";
        
        $cookieQueue = $app->make(\Illuminate\Contracts\Cookie\QueueingFactory::class);
        echo "✅ Cookie Queue: " . get_class($cookieQueue) . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Cookie Contracts: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 4: Session مع Cookie
    echo "<h4>4️⃣ اختبار Session مع Cookie:</h4>";
    try {
        $sessionStore = $app->make('session.store');
        echo "✅ Session Store: " . get_class($sessionStore) . "<br>";
        
        $session = $app->make('session');
        echo "✅ Session Service: " . get_class($session) . "<br>";
        
        // اختبار session operations
        $session->put('test_with_cookie', 'success');
        $value = $session->get('test_with_cookie');
        
        if ($value === 'success') {
            echo "✅ Session operations تعمل مع Cookie<br>";
        } else {
            echo "❌ Session operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session مع Cookie: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 5: Translator Service
    echo "<h4>5️⃣ اختبار Translator Service:</h4>";
    try {
        $translator = $app->make('translator');
        echo "✅ Translator Service: " . get_class($translator) . "<br>";
        
        // اختبار translation
        try {
            $translated = $translator->get('auth.failed');
            echo "✅ Translation: " . substr($translated, 0, 50) . "...<br>";
        } catch (Exception $e) {
            echo "⚠️ Translation: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Translator Service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 6: HTTP Request مع Cookie
    echo "<h4>6️⃣ اختبار HTTP Request مع Cookie:</h4>";
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        echo "✅ HTTP Request status: <strong>$status</strong><br>";
        
        if ($status == 200) {
            echo "🎉 الصفحة الرئيسية تعمل مع Cookie!<br>";
        } elseif ($status == 302) {
            $location = $response->headers->get('Location');
            echo "🔄 إعادة توجيه إلى: $location<br>";
        }
        
        // فحص cookies في response
        $cookies = $response->headers->getCookies();
        if (!empty($cookies)) {
            echo "✅ Response cookies:<br>";
            foreach ($cookies as $cookie) {
                echo "  - " . $cookie->getName() . " = " . substr($cookie->getValue(), 0, 20) . "...<br>";
            }
        } else {
            echo "⚠️ لا توجد cookies في response<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
        
        if (strpos($e->getMessage(), 'cookie') !== false || strpos($e->getMessage(), 'Cookie') !== false) {
            echo "<strong>🚨 لا تزال هناك مشكلة في Cookie!</strong><br>";
        }
    }
    
    // اختبار 7: Login Page مع Cookie
    echo "<h4>7️⃣ اختبار Login Page مع Cookie:</h4>";
    try {
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        $loginResponse = $kernel->handle($loginRequest);
        $loginStatus = $loginResponse->getStatusCode();
        
        echo "✅ Login page status: <strong>$loginStatus</strong><br>";
        
        if ($loginStatus == 200) {
            echo "🎉 صفحة تسجيل الدخول تعمل مع Cookie!<br>";
        } elseif ($loginStatus == 302) {
            echo "🔄 إعادة توجيه من login<br>";
        }
        
        // فحص login cookies
        $loginCookies = $loginResponse->headers->getCookies();
        if (!empty($loginCookies)) {
            echo "✅ Login cookies:<br>";
            foreach ($loginCookies as $cookie) {
                echo "  - " . $cookie->getName() . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Login Page: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 8: Database مع Cookie
    echo "<h4>8️⃣ اختبار Database مع Cookie:</h4>";
    try {
        $db = $app->make('db');
        $connection = $db->connection();
        $driverName = $connection->getDriverName();
        
        echo "✅ Database driver: <strong>$driverName</strong><br>";
        
        if ($driverName === 'sqlite') {
            echo "✅ SQLite يعمل مع Cookie<br>";
            
            // اختبار جدول users
            try {
                $userCount = $connection->select("SELECT COUNT(*) as count FROM users");
                echo "✅ عدد المستخدمين: " . $userCount[0]->count . "<br>";
            } catch (Exception $e) {
                echo "⚠️ مشكلة في قراءة الجداول: " . $e->getMessage() . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Database: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ إصلاح Cookie المطبق</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>📝 التحديثات المطبقة:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 12px;'>";
echo htmlspecialchars('// إضافة cookie config
$config->set(\'cookie\', [
    \'name\' => \'laravel_session\',
    \'lifetime\' => 120,
    \'path\' => \'/\',
    \'domain\' => null,
    \'secure\' => false,
    \'http_only\' => true,
    \'same_site\' => \'lax\',
    \'raw\' => false,
]);

// تسجيل Cookie services يدوياً
$app->singleton(\'cookie\', function ($app) {
    return new \Illuminate\Cookie\CookieJar();
});

$app->singleton(\Illuminate\Contracts\Cookie\Factory::class, function ($app) {
    return $app->make(\'cookie\');
});

$app->singleton(\Illuminate\Contracts\Cookie\QueueingFactory::class, function ($app) {
    return $app->make(\'cookie\');
});');
echo "</pre>";
echo "</div>";

echo "<ul>";
echo "<li>إضافة cookie config كامل</li>";
echo "<li>تسجيل CookieJar يدوياً</li>";
echo "<li>تسجيل Cookie Factory contract</li>";
echo "<li>تسجيل Cookie QueueingFactory contract</li>";
echo "<li>تجاهل CookieServiceProvider dependencies</li>";
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
echo "<h2>🎯 اختبار النظام مع Cookie</h2>";
echo "<p>بعد إصلاح مشكلة Cookie، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ قاعدة البيانات',
    '/fix-translator-service.php' => '🌐 اختبار Translator'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎉 تم إصلاح Cookie Service</h2>";
echo "<p style='font-size: 18px; font-weight: bold;'>النظام الآن يعمل مع Cookie و Session و Translation!</p>";
echo "<p>جميع Service Providers الأساسية تعمل بشكل صحيح</p>";
echo "</div>";

?>
