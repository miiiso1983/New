<?php

echo "<h1>🔧 الحل النهائي لمشكلة View Paths</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🚨 مشكلة View Paths مستمرة</h2>";
echo "<p>FileViewFinder::__construct(): Argument #2 (\$paths) must be of type array, null given</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة View Paths</h2>";

echo "<p><strong>المشكلة:</strong> ViewServiceProvider يحاول قراءة config قبل أن يتم تسجيل config بشكل صحيح</p>";

// إنشاء مجلدات view إذا لم تكن موجودة
$viewDirs = [
    '../resources',
    '../resources/views',
    '../storage/framework/views',
    '../storage/framework/cache/data',
    '../storage/framework/sessions'
];

echo "<h3>📂 إنشاء المجلدات المطلوبة:</h3>";
foreach ($viewDirs as $dir) {
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

// إنشاء ملف view تجريبي
$testViewPath = '../resources/views/welcome.blade.php';
if (!file_exists($testViewPath)) {
    $testViewContent = '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .container { background: rgba(255,255,255,0.1); padding: 40px; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 مرحباً بك في نظام إدارة الصيدلية</h1>
        <p>تم تحميل Laravel بنجاح مع SQLite!</p>
        <p>الوقت الحالي: {{ date("Y-m-d H:i:s") }}</p>
        <a href="/login" style="display: inline-block; padding: 15px 30px; background: #ff9800; color: white; text-decoration: none; border-radius: 8px; margin: 10px;">تسجيل الدخول</a>
    </div>
</body>
</html>';
    
    if (file_put_contents($testViewPath, $testViewContent)) {
        echo "✅ تم إنشاء ملف welcome.blade.php<br>";
    } else {
        echo "❌ فشل في إنشاء ملف welcome.blade.php<br>";
    }
} else {
    echo "✅ ملف welcome.blade.php موجود<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ إنشاء Bootstrap بديل بدون ViewServiceProvider</h2>";

echo "<p>إذا استمرت مشكلة ViewServiceProvider، يمكن إنشاء bootstrap بديل بدون views:</p>";

// إنشاء bootstrap بديل بدون ViewServiceProvider
$bootstrapNoViews = '<?php

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

// تسجيل config
$app->instance(\'config\', $config);

// تسجيل files service
$app->singleton(\'files\', function () {
    return new \Illuminate\Filesystem\Filesystem();
});

// تهيئة Facades
\Illuminate\Support\Facades\Facade::clearResolvedInstances();
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

// تسجيل Service Providers الأساسية (بدون ViewServiceProvider)
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
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

if (file_put_contents('../bootstrap/app-no-views.php', $bootstrapNoViews)) {
    echo "✅ تم إنشاء bootstrap/app-no-views.php<br>";
    echo "<p>يمكن استخدام هذا Bootstrap البديل إذا استمرت مشاكل Views</p>";
} else {
    echo "❌ فشل في إنشاء bootstrap بديل<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Bootstrap بدون Views</h2>";

try {
    echo "<h3>📁 تحميل Laravel بدون ViewServiceProvider:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-views.php';
    echo "✅ تم تحميل Laravel بدون ViewServiceProvider<br>";
    
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
        }
        
    } catch (Exception $e) {
        echo "❌ Database service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel بدون views
    echo "<h3>🌐 اختبار HTTP Kernel بدون Views:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ HTTP Request بدون Views - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل بدون Views!<br>";
            } elseif ($status == 302) {
                echo "🔄 إعادة توجيه (طبيعي)<br>";
            } else {
                echo "⚠️ كود استجابة: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'view') !== false || strpos($e->getMessage(), 'FileViewFinder') !== false) {
                echo "<strong>🚨 لا تزال هناك مشكلة في Views!</strong><br>";
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
echo "<li>تسجيل FoundationServiceProvider أولاً لضمان توفر helper functions</li>";
echo "<li>إعادة ترتيب تسجيل Service Providers</li>";
echo "<li>إنشاء bootstrap بديل بدون ViewServiceProvider</li>";
echo "<li>إنشاء جميع المجلدات المطلوبة للـ views</li>";
echo "<li>إنشاء ملف welcome.blade.php تجريبي</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الحلول</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/fix-withfacades-error.php' => '🔧 إصلاح withFacades',
    '/create-database-tables.php' => '🗄️ إنشاء الجداول'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
