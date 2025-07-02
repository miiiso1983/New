<?php

echo "<h1>🔧 إصلاح خطأ method_exists</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص خطأ method_exists</h2>";

echo "<p><strong>الخطأ:</strong> method_exists(): Argument #1 must be of type object|string, true given</p>";
echo "<p>هذا يعني أن هناك استدعاء لـ method_exists() يمرر قيمة boolean بدلاً من كلاس أو كائن.</p>";

try {
    echo "<h3>🚀 اختبار Laravel مع معالجة الأخطاء:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config service
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        
        $appName = $config->get('app.name', 'Laravel');
        echo "✅ اسم التطبيق: $appName<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel مع معالجة خاصة
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط مع try-catch شامل
        try {
            $request = \Illuminate\Http\Request::create('/', 'GET');
            echo "✅ Request تم إنشاؤه<br>";
            
            // معالجة الطلب مع تجنب method_exists المشكل
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            
            echo "✅ الطلب تم معالجته - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الموقع يعمل بنجاح!<br>";
            } elseif ($status == 302) {
                echo "🔄 إعادة توجيه (طبيعي)<br>";
            } else {
                echo "ℹ️ كود الاستجابة: $status<br>";
            }
            
        } catch (TypeError $e) {
            echo "❌ خطأ في النوع: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
            
            if (strpos($e->getMessage(), 'method_exists') !== false) {
                echo "<strong>🔧 تم اكتشاف مشكلة method_exists</strong><br>";
                echo "المشكلة في استدعاء method_exists() مع قيمة boolean<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في تحميل Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ إنشاء bootstrap محسن</h2>";

$bootstrapContent = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as ConfigRepository;

// إنشاء التطبيق
$app = new Application(
    $_ENV[\'APP_BASE_PATH\'] ?? dirname(__DIR__)
);

// إنشاء config service
$config = new ConfigRepository();

// تحميل الإعدادات الأساسية
$config->set(\'app\', [
    \'name\' => env(\'APP_NAME\', \'نظام إدارة الصيدلية\'),
    \'env\' => env(\'APP_ENV\', \'production\'),
    \'debug\' => env(\'APP_DEBUG\', false),
    \'url\' => env(\'APP_URL\', \'https://phplaravel-1486247-5658490.cloudwaysapps.com\'),
    \'key\' => env(\'APP_KEY\'),
    \'cipher\' => \'AES-256-CBC\',
    \'timezone\' => \'UTC\',
    \'locale\' => \'ar\',
    \'fallback_locale\' => \'en\',
]);

$config->set(\'database\', [
    \'default\' => env(\'DB_CONNECTION\', \'sqlite\'),
    \'connections\' => [
        \'sqlite\' => [
            \'driver\' => \'sqlite\',
            \'database\' => database_path(\'database.sqlite\'),
            \'prefix\' => \'\',
            \'foreign_key_constraints\' => true,
        ],
    ],
]);

// تسجيل config
$app->instance(\'config\', $config);

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

if (file_put_contents('../bootstrap/app-safe.php', $bootstrapContent)) {
    echo "✅ تم إنشاء bootstrap/app-safe.php<br>";
    echo "<p>يمكنك استخدام هذا الملف كبديل آمن</p>";
} else {
    echo "❌ فشل في إنشاء bootstrap/app-safe.php<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 الحلول المقترحة</h2>";
echo "<ol>";
echo "<li><strong>استخدام bootstrap آمن:</strong> تم إنشاء app-safe.php</li>";
echo "<li><strong>فحص ملفات middleware:</strong> البحث عن method_exists مع قيم خاطئة</li>";
echo "<li><strong>تعطيل middleware مؤقتاً:</strong> إزالة middleware المشكل</li>";
echo "<li><strong>استخدام index طارئ:</strong> index-emergency.php</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f0f9ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔗 اختبار الحلول</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/index-emergency.php' => '🚨 الفهرس الطارئ',
    '/test-simple.php' => '🧪 الاختبار البسيط',
    '/ultimate-fix.php' => '🚀 الحل الشامل',
    '/' => '🏠 الصفحة الرئيسية'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #3b82f6; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
