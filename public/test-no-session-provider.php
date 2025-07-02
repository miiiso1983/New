<?php

echo "<h1>🔧 اختبار Bootstrap بدون SessionServiceProvider</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 الحل الأكثر جذرية</h2>";
echo "<p>إزالة SessionServiceProvider نهائياً وإنشاء Session services يدوياً بالكامل</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 الحل الجديد</h2>";

echo "<p><strong>المشكلة:</strong> SessionServiceProvider يحاول resolve driver من config</p>";
echo "<p><strong>الحل:</strong> تجاهل SessionServiceProvider نهائياً وإنشاء Session services مباشرة</p>";

echo "<h3>📋 الخطوات المطبقة:</h3>";
echo "<ol>";
echo "<li>عدم تسجيل SessionServiceProvider نهائياً</li>";
echo "<li>إنشاء FileSessionHandler مباشرة</li>";
echo "<li>إنشاء Session Store مباشرة</li>";
echo "<li>تسجيل Session contracts يدوياً</li>";
echo "<li>تجاهل SessionManager تماماً</li>";
echo "</ol>";

// إنشاء مجلدات session إذا لم تكن موجودة
$sessionDirs = [
    '../storage',
    '../storage/framework',
    '../storage/framework/sessions',
    '../storage/framework/cache',
    '../storage/framework/cache/data',
    '../storage/framework/views',
    '../storage/app',
    '../storage/app/public'
];

echo "<h3>📂 إنشاء مجلدات Storage:</h3>";
foreach ($sessionDirs as $dir) {
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

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Bootstrap الجديد</h2>";

try {
    echo "<h3>📁 تحميل Laravel بدون SessionServiceProvider:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-session-provider.php';
    echo "✅ تم تحميل Laravel بدون SessionServiceProvider<br>";
    
    // اختبار 1: Session Handler
    echo "<h4>1️⃣ اختبار Session Handler:</h4>";
    try {
        $sessionHandler = $app->make('session.handler');
        echo "✅ Session Handler: " . get_class($sessionHandler) . "<br>";
        
        // اختبار handler operations
        $sessionId = 'test_' . uniqid();
        $testData = 'test_data_' . time();
        
        $sessionHandler->write($sessionId, $testData);
        $readData = $sessionHandler->read($sessionId);
        
        if ($readData === $testData) {
            echo "✅ Session Handler operations تعمل<br>";
        } else {
            echo "❌ Session Handler operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Handler: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 2: Session Store
    echo "<h4>2️⃣ اختبار Session Store:</h4>";
    try {
        $sessionStore = $app->make('session.store');
        echo "✅ Session Store: " . get_class($sessionStore) . "<br>";
        
        // اختبار store operations
        $sessionStore->put('test_store_key', 'test_store_value');
        $value = $sessionStore->get('test_store_key');
        
        if ($value === 'test_store_value') {
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
    
    // اختبار 3: Session Service
    echo "<h4>3️⃣ اختبار Session Service:</h4>";
    try {
        $session = $app->make('session');
        echo "✅ Session Service: " . get_class($session) . "<br>";
        
        // اختبار session operations
        $session->put('test_session_key', 'test_session_value');
        $value = $session->get('test_session_key');
        
        if ($value === 'test_session_value') {
            echo "✅ Session Service operations تعمل<br>";
        } else {
            echo "❌ Session Service operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 4: Session Contract
    echo "<h4>4️⃣ اختبار Session Contract:</h4>";
    try {
        $sessionContract = $app->make(\Illuminate\Contracts\Session\Session::class);
        echo "✅ Session Contract: " . get_class($sessionContract) . "<br>";
        
        // اختبار contract operations
        $sessionContract->put('test_contract_key', 'test_contract_value');
        $value = $sessionContract->get('test_contract_key');
        
        if ($value === 'test_contract_value') {
            echo "✅ Session Contract operations تعمل<br>";
        } else {
            echo "❌ Session Contract operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Contract: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 5: HTTP Request
    echo "<h4>5️⃣ اختبار HTTP Request:</h4>";
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        echo "✅ HTTP Request status: <strong>$status</strong><br>";
        
        if ($status == 200) {
            echo "🎉 الصفحة الرئيسية تعمل بدون SessionServiceProvider!<br>";
        } elseif ($status == 302) {
            $location = $response->headers->get('Location');
            echo "🔄 إعادة توجيه إلى: $location<br>";
        }
        
        // فحص session cookies
        $cookies = $response->headers->getCookies();
        if (!empty($cookies)) {
            echo "✅ Session cookies تم تعيينها:<br>";
            foreach ($cookies as $cookie) {
                echo "  - " . $cookie->getName() . "<br>";
            }
        } else {
            echo "⚠️ لا توجد session cookies<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
        
        if (strpos($e->getMessage(), 'session') !== false || strpos($e->getMessage(), 'SessionManager') !== false) {
            echo "<strong>🚨 لا تزال هناك مشكلة في Session!</strong><br>";
        }
    }
    
    // اختبار 6: Login Page
    echo "<h4>6️⃣ اختبار Login Page:</h4>";
    try {
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        $loginResponse = $kernel->handle($loginRequest);
        $loginStatus = $loginResponse->getStatusCode();
        
        echo "✅ Login page status: <strong>$loginStatus</strong><br>";
        
        if ($loginStatus == 200) {
            echo "🎉 صفحة تسجيل الدخول تعمل بدون SessionServiceProvider!<br>";
        } elseif ($loginStatus == 302) {
            echo "🔄 إعادة توجيه من login<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Login Page: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل الأكثر جذرية</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>📝 الكود المطبق:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 12px;'>";
echo htmlspecialchars('// إنشاء Session services يدوياً بالكامل بدون SessionServiceProvider
$sessionPath = dirname(__DIR__) . \'/storage/framework/sessions\';

// تسجيل Session Handler يدوياً
$app->singleton(\'session.handler\', function ($app) use ($sessionPath) {
    $files = $app->make(\'files\');
    return new \Illuminate\Session\FileSessionHandler($files, $sessionPath, 120);
});

// تسجيل Session Store يدوياً
$app->singleton(\'session.store\', function ($app) {
    $handler = $app->make(\'session.handler\');
    $store = new \Illuminate\Session\Store(\'laravel_session\', $handler);
    $store->start();
    return $store;
});

// تسجيل Session Manager يدوياً (بدون driver resolution)
$app->singleton(\'session\', function ($app) {
    return $app->make(\'session.store\');
});

// تسجيل Session Contract
$app->singleton(\Illuminate\Contracts\Session\Session::class, function ($app) {
    return $app->make(\'session.store\');
});');
echo "</pre>";
echo "</div>";

echo "<ul>";
echo "<li>عدم تسجيل SessionServiceProvider نهائياً</li>";
echo "<li>إنشاء FileSessionHandler مباشرة</li>";
echo "<li>إنشاء Session Store مباشرة مع start()</li>";
echo "<li>تسجيل Session contracts يدوياً</li>";
echo "<li>تجاهل SessionManager driver resolution</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الحل الأكثر جذرية</h2>";
echo "<p>إذا نجح الاختبار أعلاه، فهذا هو الحل النهائي الأكثر استقراراً!</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ قاعدة البيانات'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🔧 الحل الأكثر جذرية</h2>";
echo "<p style='font-size: 18px; font-weight: bold;'>إزالة SessionServiceProvider نهائياً!</p>";
echo "<p>إذا نجح هذا الحل، فلن تحدث مشاكل Session أبداً</p>";
echo "</div>";

?>
