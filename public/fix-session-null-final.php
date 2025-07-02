<?php

echo "<h1>🔧 الحل النهائي لمشكلة Session NULL Driver</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة Session NULL Driver مستمرة</h2>";
echo "<p>SessionManager لا يزال لا يستطيع resolve الـ driver - الحل النهائي</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 الحل النهائي الشامل</h2>";

echo "<p><strong>المشكلة:</strong> SessionManager يبحث عن session config ولا يجده</p>";
echo "<p><strong>الحل النهائي:</strong> إضافة session config كامل + تسجيل driver يدوياً</p>";

echo "<h3>📋 الحل الشامل المطبق:</h3>";
echo "<ol>";
echo "<li>إضافة session config كامل في بداية bootstrap</li>";
echo "<li>إضافة session config مرة أخرى في SessionManager</li>";
echo "<li>تسجيل file driver يدوياً في SessionManager</li>";
echo "<li>ضمان توفر جميع session config keys</li>";
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

echo "<h3>📂 التأكد من مجلدات Storage:</h3>";
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
echo "<h2>🧪 اختبار الحل النهائي</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع الحل النهائي:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-session-provider.php';
    echo "✅ تم تحميل Laravel مع الحل النهائي<br>";
    
    // اختبار 1: Session Config
    echo "<h4>1️⃣ اختبار Session Config الكامل:</h4>";
    try {
        $config = $app->make('config');
        
        $sessionDriver = $config->get('session.driver');
        $sessionFiles = $config->get('session.files');
        $sessionLifetime = $config->get('session.lifetime');
        $sessionCookie = $config->get('session.cookie');
        
        echo "✅ Session driver: <strong>$sessionDriver</strong><br>";
        echo "✅ Session files: $sessionFiles<br>";
        echo "✅ Session lifetime: <strong>$sessionLifetime</strong><br>";
        echo "✅ Session cookie: <strong>$sessionCookie</strong><br>";
        
        if ($sessionDriver === 'file') {
            echo "✅ Session driver محدد بشكل صحيح<br>";
        } else {
            echo "❌ Session driver غير محدد: $sessionDriver<br>";
        }
        
        if (is_dir($sessionFiles) && is_writable($sessionFiles)) {
            echo "✅ Session files directory متاح وقابل للكتابة<br>";
        } else {
            echo "❌ Session files directory غير متاح<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Config: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 2: SessionManager مع Config
    echo "<h4>2️⃣ اختبار SessionManager مع Config:</h4>";
    try {
        $sessionManager = $app->make('session');
        echo "✅ SessionManager: " . get_class($sessionManager) . "<br>";
        
        if ($sessionManager instanceof \Illuminate\Session\SessionManager) {
            echo "✅ SessionManager نوع صحيح<br>";
            
            // اختبار getDefaultDriver
            try {
                $defaultDriver = $sessionManager->getDefaultDriver();
                echo "✅ Default driver: <strong>$defaultDriver</strong><br>";
                
                if ($defaultDriver === 'file') {
                    echo "✅ Default driver صحيح<br>";
                } else {
                    echo "❌ Default driver خاطئ: $defaultDriver<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ Default driver: " . $e->getMessage() . "<br>";
            }
            
            // اختبار driver resolution
            try {
                $fileDriver = $sessionManager->driver('file');
                echo "✅ File driver resolved: " . get_class($fileDriver) . "<br>";
                
                if ($fileDriver instanceof \Illuminate\Session\Store) {
                    echo "✅ File driver نوع صحيح<br>";
                } else {
                    echo "❌ File driver نوع خاطئ<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ File driver resolution: " . $e->getMessage() . "<br>";
            }
            
        } else {
            echo "❌ SessionManager نوع خاطئ<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ SessionManager: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 3: Session Store
    echo "<h4>3️⃣ اختبار Session Store:</h4>";
    try {
        $sessionStore = $app->make('session.store');
        echo "✅ Session Store: " . get_class($sessionStore) . "<br>";
        
        // اختبار session operations
        $sessionStore->put('test_final_fix', 'success');
        $value = $sessionStore->get('test_final_fix');
        
        if ($value === 'success') {
            echo "✅ Session Store operations تعمل<br>";
        } else {
            echo "❌ Session Store operations فاشلة<br>";
        }
        
        // اختبار session save
        try {
            $sessionStore->save();
            echo "✅ Session save يعمل<br>";
            
            $sessionId = $sessionStore->getId();
            echo "✅ Session ID: " . substr($sessionId, 0, 20) . "...<br>";
            
        } catch (Exception $e) {
            echo "❌ Session save: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Store: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 4: StartSession Middleware
    echo "<h4>4️⃣ اختبار StartSession Middleware:</h4>";
    try {
        $sessionManager = $app->make('session');
        $startSessionMiddleware = new \Illuminate\Session\Middleware\StartSession($sessionManager);
        echo "✅ StartSession Middleware: " . get_class($startSessionMiddleware) . "<br>";
        
        echo "✅ StartSession middleware يمكن إنشاؤه بنجاح<br>";
        
    } catch (Exception $e) {
        echo "❌ StartSession Middleware: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 5: HTTP Request النهائي
    echo "<h4>5️⃣ اختبار HTTP Request النهائي:</h4>";
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        echo "✅ HTTP Request status: <strong>$status</strong><br>";
        
        if ($status == 200) {
            echo "🎉 الصفحة الرئيسية تعمل مع Session النهائي!<br>";
        } elseif ($status == 302) {
            $location = $response->headers->get('Location');
            echo "🔄 إعادة توجيه إلى: $location<br>";
        } else {
            echo "⚠️ كود استجابة غير متوقع: $status<br>";
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
        
        if (strpos($e->getMessage(), 'NULL driver') !== false || strpos($e->getMessage(), 'SessionManager') !== false) {
            echo "<strong>🚨 لا تزال هناك مشكلة في Session NULL driver!</strong><br>";
        }
    }
    
    // اختبار 6: Login Page النهائي
    echo "<h4>6️⃣ اختبار Login Page النهائي:</h4>";
    try {
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        $loginResponse = $kernel->handle($loginRequest);
        $loginStatus = $loginResponse->getStatusCode();
        
        echo "✅ Login page status: <strong>$loginStatus</strong><br>";
        
        if ($loginStatus == 200) {
            echo "🎉 صفحة تسجيل الدخول تعمل مع Session النهائي!<br>";
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
echo "<h2>✅ الحل النهائي الشامل</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>📝 الحل النهائي المطبق:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 11px;'>";
echo htmlspecialchars('// إضافة session config كامل في بداية bootstrap
$config->set(\'session\', [
    \'driver\' => \'file\',
    \'lifetime\' => 120,
    \'expire_on_close\' => false,
    \'encrypt\' => false,
    \'files\' => dirname(__DIR__) . \'/storage/framework/sessions\',
    \'connection\' => null,
    \'table\' => \'sessions\',
    \'store\' => null,
    \'lottery\' => [2, 100],
    \'cookie\' => \'laravel_session\',
    \'path\' => \'/\',
    \'domain\' => null,
    \'secure\' => null,
    \'http_only\' => true,
    \'same_site\' => \'lax\',
    \'partitioned\' => false,
]);

// تسجيل SessionManager مع config إضافي
$app->singleton(\'session\', function ($app) {
    $manager = new \Illuminate\Session\SessionManager($app);
    
    // إضافة session config مباشرة
    $config = $app->make(\'config\');
    $config->set(\'session.driver\', \'file\');
    $config->set(\'session.files\', dirname(__DIR__) . \'/storage/framework/sessions\');
    $config->set(\'session.lifetime\', 120);
    $config->set(\'session.cookie\', \'laravel_session\');
    
    // تسجيل file driver يدوياً
    $manager->extend(\'file\', function ($app) {
        $handler = $app->make(\'session.handler\');
        return new \Illuminate\Session\Store(\'laravel_session\', $handler);
    });
    
    return $manager;
});');
echo "</pre>";
echo "</div>";

echo "<ul>";
echo "<li>إضافة session config كامل في بداية bootstrap</li>";
echo "<li>إضافة session config مرة أخرى في SessionManager</li>";
echo "<li>تسجيل file driver يدوياً في SessionManager</li>";
echo "<li>ضمان توفر جميع session config keys</li>";
echo "<li>إنشاء جميع مجلدات storage المطلوبة</li>";
echo "<li>تسجيل SessionManager alias</li>";
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
echo "<h2>🎯 اختبار الحل النهائي</h2>";
echo "<p>إذا نجح الاختبار أعلاه، فهذا هو الحل النهائي الأكثر شمولية!</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ قاعدة البيانات',
    '/fix-session-manager.php' => '🔧 اختبار SessionManager'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🔧 الحل النهائي لـ Session NULL Driver</h2>";
echo "<p style='font-size: 18px; font-weight: bold;'>الحل الأكثر شمولية وتفصيلاً!</p>";
echo "<p>إذا نجح هذا الحل، فلن تحدث مشاكل Session مرة أخرى أبداً</p>";
echo "</div>";

?>
