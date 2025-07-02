<?php

echo "<h1>🔧 إصلاح مشكلة Session NULL Driver</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة Session NULL Driver مستمرة</h2>";
echo "<p>Unable to resolve NULL driver for [Illuminate\\Session\\SessionManager]</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Session NULL Driver</h2>";

echo "<p><strong>المشكلة:</strong> SessionManager لا يمكنه العثور على driver صحيح</p>";
echo "<p>هذا يحدث عادة عندما:</p>";
echo "<ul>";
echo "<li>Session config غير مكتمل</li>";
echo "<li>SessionServiceProvider لم يتم تسجيله بشكل صحيح</li>";
echo "<li>ترتيب تسجيل Service Providers خاطئ</li>";
echo "<li>مجلدات session غير موجودة</li>";
echo "</ul>";

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
echo "<h2>🛠️ إنشاء Bootstrap بديل مع Session محسن</h2>";

echo "<p>إنشاء bootstrap بديل مع تسجيل Session بطريقة مختلفة:</p>";

// إنشاء bootstrap بديل مع session محسن
$bootstrapSessionFixed = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as ConfigRepository;

// إنشاء التطبيق
$app = new Application(
    $_ENV[\'APP_BASE_PATH\'] ?? dirname(__DIR__)
);

// إنشاء config service
$config = new ConfigRepository();

// إعدادات app
$config->set(\'app\', [
    \'name\' => \'نظام إدارة الصيدلية\',
    \'env\' => \'production\',
    \'debug\' => false,
    \'url\' => \'https://phplaravel-1486247-5658490.cloudwaysapps.com\',
    \'key\' => \'base64:QKyZoyATcjBxA0qzfcTUPrsxush+g+1ASMVMxxjXcwk=\',
    \'cipher\' => \'AES-256-CBC\',
    \'timezone\' => \'UTC\',
    \'locale\' => \'ar\',
    \'fallback_locale\' => \'en\',
]);

// إعدادات database
$config->set(\'database\', [
    \'default\' => \'sqlite\',
    \'connections\' => [
        \'sqlite\' => [
            \'driver\' => \'sqlite\',
            \'database\' => \'/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite\',
            \'prefix\' => \'\',
            \'foreign_key_constraints\' => true,
        ],
    ],
]);

// إعدادات session محسنة
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

// إعدادات cache
$config->set(\'cache\', [
    \'default\' => \'file\',
    \'stores\' => [
        \'file\' => [
            \'driver\' => \'file\',
            \'path\' => dirname(__DIR__) . \'/storage/framework/cache/data\',
        ],
        \'array\' => [
            \'driver\' => \'array\',
            \'serialize\' => false,
        ],
    ],
    \'prefix\' => \'laravel_cache\',
]);

// تسجيل config
$app->instance(\'config\', $config);

// تسجيل files service
$app->singleton(\'files\', function () {
    return new \Illuminate\Filesystem\Filesystem();
});

// تهيئة Facades
\Illuminate\Support\Facades\Facade::clearResolvedInstances();
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

// تسجيل Service Providers بترتيب محسن
$app->register(\Illuminate\Foundation\Providers\FoundationServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);

// تسجيل Session Service Provider مع تأكيد إضافي
$app->register(\Illuminate\Session\SessionServiceProvider::class);

// تسجيل باقي Service Providers
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Validation\ValidationServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);
$app->register(\Illuminate\Hashing\HashServiceProvider::class);

// تسجيل MaintenanceMode service يدوياً
$app->singleton(
    \Illuminate\Contracts\Foundation\MaintenanceMode::class,
    \Illuminate\Foundation\MaintenanceModeManager::class
);

// تسجيل الخدمات الأساسية
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// تسجيل Application Service Providers
if (class_exists(\'App\Providers\AppServiceProvider\')) {
    $app->register(App\Providers\AppServiceProvider::class);
}
if (class_exists(\'App\Providers\RouteServiceProvider\')) {
    $app->register(App\Providers\RouteServiceProvider::class);
}

return $app;
';

if (file_put_contents('../bootstrap/app-session-fixed.php', $bootstrapSessionFixed)) {
    echo "✅ تم إنشاء bootstrap/app-session-fixed.php<br>";
    echo "<p>يمكن استخدام هذا Bootstrap البديل مع Session محسن</p>";
} else {
    echo "❌ فشل في إنشاء bootstrap بديل<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Bootstrap مع Session محسن</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع Session محسن:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-session-fixed.php';
    echo "✅ تم تحميل Laravel مع Session محسن<br>";
    
    // فحص Session config
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        $sessionDriver = $config->get('session.driver');
        echo "✅ Session driver: <strong>$sessionDriver</strong><br>";
        
        if ($sessionDriver === 'file') {
            echo "✅ Session driver محدد بشكل صحيح<br>";
        } else {
            echo "❌ Session driver غير صحيح: $sessionDriver<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار session service
    try {
        $session = $app->make('session');
        echo "✅ Session service يعمل<br>";
        echo "✅ نوع Session: " . get_class($session) . "<br>";
        
        // اختبار session manager
        try {
            $sessionManager = $app->make('session.manager');
            echo "✅ Session manager يعمل<br>";
            echo "✅ نوع Session Manager: " . get_class($sessionManager) . "<br>";
            
            // اختبار session driver
            try {
                $sessionDriver = $sessionManager->driver();
                echo "✅ Session driver يعمل<br>";
                echo "✅ نوع Session Driver: " . get_class($sessionDriver) . "<br>";
                
                // اختبار session operations
                try {
                    $sessionDriver->put('test_key', 'test_value');
                    $value = $sessionDriver->get('test_key');
                    if ($value === 'test_value') {
                        echo "✅ Session operations تعمل بنجاح<br>";
                    } else {
                        echo "⚠️ Session operations لا تعمل بشكل صحيح<br>";
                    }
                } catch (Exception $e) {
                    echo "⚠️ Session operations: " . $e->getMessage() . "<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ Session driver: " . $e->getMessage() . "<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Session manager: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel مع Session محسن
    echo "<h3>🌐 اختبار HTTP Kernel مع Session محسن:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request مع Session محسن - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل مع Session محسن!<br>";
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            } else {
                echo "⚠️ كود استجابة: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'session') !== false || strpos($e->getMessage(), 'SessionManager') !== false) {
                echo "<strong>🚨 لا تزال هناك مشكلة في Session!</strong><br>";
            }
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
echo "<h2>✅ الحلول المطبقة</h2>";
echo "<ul>";
echo "<li>إنشاء bootstrap بديل مع session config محسن</li>";
echo "<li>تسجيل SessionServiceProvider بترتيب محسن</li>";
echo "<li>إنشاء جميع مجلدات storage المطلوبة</li>";
echo "<li>تبسيط session config وإزالة التعقيدات</li>";
echo "<li>تأكيد تسجيل جميع Service Providers المطلوبة</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الحل</h2>";
echo "<p>إذا نجح الاختبار أعلاه، يمكن تحديث index.php لاستخدام Bootstrap الجديد:</p>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<code>// في public/index.php<br>";
echo "\$app = require_once __DIR__.'/../bootstrap/app-session-fixed.php';</code>";
echo "</div>";

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/fix-session-driver.php' => '🔧 إصلاح Session Driver',
    '/create-clean-database.php' => '🗄️ إنشاء قاعدة البيانات'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🔧 إصلاح Session NULL Driver</h2>";
echo "<p style='font-size: 18px; color: #dc3545; font-weight: bold;'>إذا نجح الاختبار، فالمشكلة محلولة!</p>";
echo "<p>إذا لم تنجح، فالمشكلة قد تكون في مستوى أعمق في Laravel</p>";
echo "</div>";

?>
