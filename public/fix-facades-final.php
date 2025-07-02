<?php

echo "<h1>🔧 الحل النهائي لمشكلة Facades</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة Facades مستمرة</h2>";
echo "<p>A facade root has not been set - Laravel Facades لا تزال غير مهيأة</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص شامل لمشكلة Facades</h2>";

echo "<p><strong>المشكلة:</strong> Laravel Facades تحتاج إلى تهيئة صحيحة في bootstrap</p>";

try {
    echo "<h3>📁 تحميل Laravel مع تهيئة Facades محسنة:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    // تهيئة يدوية للـ Facades قبل تحميل bootstrap
    echo "<h4>🔧 تهيئة Facades يدوياً:</h4>";
    
    // إنشاء Application instance
    $app = new \Illuminate\Foundation\Application(
        $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
    );
    
    // تهيئة Facades مباشرة
    \Illuminate\Support\Facades\Facade::clearResolvedInstances();
    \Illuminate\Support\Facades\Facade::setFacadeApplication($app);
    echo "✅ تم تعيين Facade Application يدوياً<br>";
    
    // تحميل bootstrap
    $app = require_once '../bootstrap/app-sqlite-only.php';
    echo "✅ تم تحميل Laravel bootstrap<br>";
    
    // فحص Facade Application
    try {
        $facadeApp = \Illuminate\Support\Facades\Facade::getFacadeApplication();
        if ($facadeApp) {
            echo "✅ Facade Application متاح<br>";
            echo "✅ نوع Facade App: " . get_class($facadeApp) . "<br>";
            
            if ($facadeApp === $app) {
                echo "✅ Facade Application يطابق Laravel App<br>";
            } else {
                echo "⚠️ Facade Application لا يطابق Laravel App<br>";
            }
            
        } else {
            echo "❌ Facade Application لا يزال null<br>";
        }
    } catch (Exception $e) {
        echo "❌ Facade Application check: " . $e->getMessage() . "<br>";
    }
    
    // اختبار Facades الأساسية
    echo "<h3>🧪 اختبار Facades الأساسية:</h3>";
    
    $facades = [
        'Config' => function() { return \Illuminate\Support\Facades\Config::get('app.name'); },
        'DB' => function() { return \Illuminate\Support\Facades\DB::connection()->getDriverName(); },
        'Cache' => function() { return \Illuminate\Support\Facades\Cache::get('test', 'default'); },
        'View' => function() { return \Illuminate\Support\Facades\View::exists('welcome'); },
    ];
    
    foreach ($facades as $name => $test) {
        try {
            $result = $test();
            echo "✅ $name facade يعمل - النتيجة: $result<br>";
        } catch (Exception $e) {
            echo "❌ $name facade: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'facade root') !== false) {
                echo "  🚨 مشكلة facade root في $name<br>";
            }
        }
    }
    
    // اختبار HTTP Request مع Facades
    echo "<h3>🌐 اختبار HTTP Request مع Facades:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel متاح<br>";
        
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
                echo "<strong>🚨 مشكلة Facades في HTTP Request!</strong><br>";
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

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ إنشاء Bootstrap بديل بدون Facades</h2>";

echo "<p>إذا استمرت مشكلة Facades، يمكن إنشاء bootstrap بديل لا يعتمد على Facades:</p>";

// إنشاء bootstrap بديل
$bootstrapNoFacades = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as ConfigRepository;

// إنشاء التطبيق
$app = new Application(
    $_ENV[\'APP_BASE_PATH\'] ?? dirname(__DIR__)
);

// إنشاء config service
$config = new ConfigRepository();

// إعدادات ثابتة
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

$config->set(\'session\', [
    \'driver\' => \'file\',
    \'lifetime\' => 120,
    \'expire_on_close\' => false,
    \'encrypt\' => false,
    \'files\' => dirname(__DIR__) . \'/storage/framework/sessions\',
    \'lottery\' => [2, 100],
    \'cookie\' => \'laravel_session\',
    \'path\' => \'/\',
    \'domain\' => null,
    \'secure\' => false,
    \'http_only\' => true,
    \'same_site\' => \'lax\',
]);

$config->set(\'cache\', [
    \'default\' => \'file\',
    \'stores\' => [
        \'file\' => [
            \'driver\' => \'file\',
            \'path\' => dirname(__DIR__) . \'/storage/framework/cache/data\',
        ],
    ],
    \'prefix\' => \'laravel_cache\',
]);

$config->set(\'view\', [
    \'paths\' => [
        dirname(__DIR__) . \'/resources/views\',
    ],
    \'compiled\' => dirname(__DIR__) . \'/storage/framework/views\',
]);

// تسجيل config
$app->instance(\'config\', $config);

// تسجيل files service
$app->singleton(\'files\', function () {
    return new \Illuminate\Filesystem\Filesystem();
});

// تسجيل Service Providers الأساسية فقط
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(\Illuminate\View\ViewServiceProvider::class);
$app->register(\Illuminate\Validation\ValidationServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);
$app->register(\Illuminate\Hashing\HashServiceProvider::class);

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

return $app;
';

if (file_put_contents('../bootstrap/app-no-facades.php', $bootstrapNoFacades)) {
    echo "✅ تم إنشاء bootstrap/app-no-facades.php<br>";
    echo "<p>يمكن استخدام هذا Bootstrap البديل إذا استمرت مشاكل Facades</p>";
} else {
    echo "❌ فشل في إنشاء bootstrap بديل<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحلول المطبقة</h2>";
echo "<ul>";
echo "<li>إضافة Facade::clearResolvedInstances() قبل setFacadeApplication</li>";
echo "<li>إضافة \$app->withFacades() لتهيئة aliases</li>";
echo "<li>إضافة \$app->boot() لتهيئة Application</li>";
echo "<li>تأكيد تهيئة Facades بعد تسجيل Service Providers</li>";
echo "<li>إنشاء bootstrap بديل بدون Facades</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الحلول</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/fix-view-paths.php' => '🔧 إصلاح View Paths',
    '/create-database-tables.php' => '🗄️ إنشاء الجداول'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
