<?php

echo "<h1>🔧 إصلاح Session يدوياً</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 حل جذري لمشكلة Session</h2>";
echo "<p>تسجيل Session services يدوياً بدلاً من الاعتماد على SessionServiceProvider</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 الحل الجذري المطبق</h2>";

echo "<p><strong>المشكلة:</strong> SessionServiceProvider لا يسجل Session driver بشكل صحيح</p>";
echo "<p><strong>الحل:</strong> تسجيل Session services يدوياً قبل SessionServiceProvider</p>";

echo "<h3>📋 الخطوات المطبقة:</h3>";
echo "<ol>";
echo "<li>تسجيل SessionManager يدوياً</li>";
echo "<li>تسجيل FileSessionHandler يدوياً</li>";
echo "<li>تسجيل Session Store يدوياً</li>";
echo "<li>ثم تسجيل SessionServiceProvider</li>";
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
echo "<h2>🧪 اختبار Session اليدوي</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع Session يدوي:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-session-fixed.php';
    echo "✅ تم تحميل Laravel مع Session يدوي<br>";
    
    // فحص Session config
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        $sessionDriver = $config->get('session.driver');
        echo "✅ Session driver config: <strong>$sessionDriver</strong><br>";
        
        $sessionFiles = $config->get('session.files');
        echo "✅ Session files path: $sessionFiles<br>";
        
        if (is_dir($sessionFiles)) {
            echo "✅ مجلد Session files موجود<br>";
        } else {
            echo "❌ مجلد Session files غير موجود<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار session service
    try {
        $session = $app->make('session');
        echo "✅ Session service يعمل<br>";
        echo "✅ نوع Session: " . get_class($session) . "<br>";
        
        // اختبار session store
        try {
            $sessionStore = $app->make('session.store');
            echo "✅ Session store يعمل<br>";
            echo "✅ نوع Session Store: " . get_class($sessionStore) . "<br>";
            
            // اختبار session operations
            try {
                $sessionStore->put('test_manual_key', 'test_manual_value');
                $value = $sessionStore->get('test_manual_key');
                if ($value === 'test_manual_value') {
                    echo "✅ Session operations تعمل بنجاح!<br>";
                    
                    // اختبار session save
                    $sessionStore->save();
                    echo "✅ Session save يعمل<br>";
                    
                    // فحص ملف session
                    $sessionId = $sessionStore->getId();
                    $sessionFile = $sessionFiles . '/laravel_session' . $sessionId;
                    if (file_exists($sessionFile)) {
                        echo "✅ ملف Session تم إنشاؤه: " . basename($sessionFile) . "<br>";
                    } else {
                        echo "⚠️ ملف Session لم يتم إنشاؤه<br>";
                    }
                    
                } else {
                    echo "⚠️ Session operations لا تعمل بشكل صحيح<br>";
                }
            } catch (Exception $e) {
                echo "❌ Session operations: " . $e->getMessage() . "<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Session store: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel مع Session يدوي
    echo "<h3>🌐 اختبار HTTP Kernel مع Session يدوي:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request مع Session يدوي - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل مع Session يدوي!<br>";
                
                // فحص session headers
                $cookies = $response->headers->getCookies();
                if (!empty($cookies)) {
                    echo "✅ Session cookies تم تعيينها<br>";
                    foreach ($cookies as $cookie) {
                        echo "  - Cookie: " . $cookie->getName() . "<br>";
                    }
                } else {
                    echo "⚠️ لا توجد session cookies<br>";
                }
                
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            } else {
                echo "⚠️ كود استجابة: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
            
            if (strpos($e->getMessage(), 'session') !== false || strpos($e->getMessage(), 'SessionManager') !== false) {
                echo "<strong>🚨 لا تزال هناك مشكلة في Session!</strong><br>";
            }
        }
        
        // اختبار صفحة تسجيل الدخول
        echo "<h3>🔐 اختبار صفحة تسجيل الدخول مع Session يدوي:</h3>";
        
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        
        try {
            $loginResponse = $kernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول مع Session يدوي - كود: $loginStatus<br>";
            
            if ($loginStatus == 200) {
                echo "🎉 صفحة تسجيل الدخول تعمل مع Session يدوي!<br>";
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
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحل الجذري المطبق</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>🔧 تسجيل Session يدوياً:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
echo "// تسجيل SessionManager يدوياً
\$app->singleton('session', function (\$app) {
    return new \\Illuminate\\Session\\SessionManager(\$app);
});

// تسجيل Session Store يدوياً
\$app->singleton('session.store', function (\$app) {
    \$config = \$app->make('config');
    \$files = \$app->make('files');
    
    // إنشاء file session handler يدوياً
    \$handler = new \\Illuminate\\Session\\FileSessionHandler(
        \$files,
        \$config->get('session.files'),
        \$config->get('session.lifetime')
    );
    
    return new \\Illuminate\\Session\\Store(
        \$config->get('session.cookie'),
        \$handler
    );
});";
echo "</pre>";
echo "</div>";

echo "<ul>";
echo "<li>تسجيل SessionManager يدوياً قبل SessionServiceProvider</li>";
echo "<li>تسجيل FileSessionHandler يدوياً</li>";
echo "<li>تسجيل Session Store يدوياً</li>";
echo "<li>إنشاء جميع مجلدات storage المطلوبة</li>";
echo "<li>تجاوز مشاكل SessionServiceProvider</li>";
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
echo "<h2>🎯 اختبار الحل الجذري</h2>";
echo "<p>إذا نجح الاختبار أعلاه، فالمشكلة محلولة نهائياً!</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ إنشاء قاعدة البيانات'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🔧 الحل الجذري لـ Session</h2>";
echo "<p style='font-size: 18px; font-weight: bold;'>تسجيل Session services يدوياً - الحل النهائي!</p>";
echo "<p>إذا نجح هذا الحل، فلن تحدث مشاكل Session مرة أخرى</p>";
echo "</div>";

?>
