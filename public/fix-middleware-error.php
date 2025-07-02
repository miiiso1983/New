<?php

echo "<h1>🔧 إصلاح مشاكل Middleware Pipeline</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشاكل Middleware</h2>";

try {
    echo "<p>📁 تحميل Laravel...</p>";
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    // فحص HTTP Kernel
    echo "<h3>🔧 فحص HTTP Kernel:</h3>";
    
    try {
        $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel تم تحميله<br>";
        
        // فحص middleware المسجلة
        $reflection = new ReflectionClass($httpKernel);
        $middlewareProperty = $reflection->getProperty('middleware');
        $middlewareProperty->setAccessible(true);
        $middleware = $middlewareProperty->getValue($httpKernel);
        
        echo "<h4>📋 Middleware المسجلة:</h4>";
        foreach ($middleware as $index => $middlewareClass) {
            echo "- $middlewareClass<br>";
            
            // فحص وجود الكلاس
            if (!class_exists($middlewareClass)) {
                echo "  ❌ الكلاس غير موجود<br>";
            } else {
                echo "  ✅ الكلاس موجود<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ مشكلة في HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
    // اختبار middleware واحد تلو الآخر
    echo "<h3>🧪 اختبار Middleware منفردة:</h3>";
    
    $middlewareToTest = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \App\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \App\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SetLocale::class,
    ];
    
    foreach ($middlewareToTest as $middlewareClass) {
        try {
            if (class_exists($middlewareClass)) {
                $middleware = new $middlewareClass();
                echo "✅ $middlewareClass - يمكن إنشاؤه<br>";
            } else {
                echo "❌ $middlewareClass - الكلاس غير موجود<br>";
            }
        } catch (Exception $e) {
            echo "❌ $middlewareClass - خطأ: " . $e->getMessage() . "<br>";
        }
    }
    
    // اختبار طلب بسيط بدون middleware
    echo "<h3>🌐 اختبار طلب بسيط:</h3>";
    
    try {
        // إنشاء طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('Accept', 'text/html');
        
        echo "✅ تم إنشاء HTTP Request<br>";
        
        // محاولة معالجة الطلب مع تجاهل middleware المشكلة
        try {
            $response = $httpKernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ معالجة الطلب نجحت - كود: $status<br>";
            
        } catch (Exception $e) {
            echo "❌ خطأ في معالجة الطلب: " . $e->getMessage() . "<br>";
            
            // إذا كان الخطأ في HandleCors
            if (strpos($e->getMessage(), 'HandleCors') !== false || strpos($e->getTraceAsString(), 'HandleCors') !== false) {
                echo "<strong>🔧 تم اكتشاف مشكلة في HandleCors middleware</strong><br>";
                echo "سيتم إنشاء HTTP Kernel محسن بدون middleware المشكلة...<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ خطأ في إنشاء الطلب: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

// إنشاء HTTP Kernel محسن
echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔧 إنشاء HTTP Kernel محسن</h2>";

$kernelContent = '<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class KernelFixed extends HttpKernel
{
    /**
     * The application\'s global HTTP middleware stack.
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        // \Illuminate\Http\Middleware\HandleCors::class, // معطل مؤقتاً
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \App\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \App\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SetLocale::class,
    ];

    /**
     * The application\'s route middleware groups.
     */
    protected $middlewareGroups = [
        \'web\' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\CustomVerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        \'api\' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.\':api\',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application\'s middleware aliases.
     */
    protected $middlewareAliases = [
        \'auth\' => \App\Http\Middleware\Authenticate::class,
        \'auth.basic\' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        \'auth.session\' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        \'cache.headers\' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        \'can\' => \Illuminate\Auth\Middleware\Authorize::class,
        \'guest\' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        \'password.confirm\' => \Illuminate\Auth\Middleware\RequirePassword::class,
        \'signed\' => \App\Http\Middleware\ValidateSignature::class,
        \'throttle\' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \'verified\' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // Custom middleware aliases
        \'super_admin\' => \App\Http\Middleware\SuperAdminMiddleware::class,
        \'super_admin.permission\' => \App\Http\Middleware\SuperAdminPermission::class,
        \'tenant.scope\' => \App\Http\Middleware\TenantScope::class,
        \'permission\' => \App\Http\Middleware\CheckPermission::class,
        \'security\' => \App\Http\Middleware\SecurityMiddleware::class,
    ];
}';

if (file_put_contents('../app/Http/KernelFixed.php', $kernelContent)) {
    echo "✅ تم إنشاء KernelFixed.php<br>";
} else {
    echo "❌ فشل في إنشاء KernelFixed.php<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الحلول المطبقة</h2>";
echo "<ul>";
echo "<li>تشخيص مشاكل middleware pipeline</li>";
echo "<li>إنشاء HTTP Kernel محسن بدون middleware المشكلة</li>";
echo "<li>تعطيل HandleCors middleware مؤقتاً</li>";
echo "<li>فحص جميع middleware للتأكد من وجودها</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 الخطوات التالية</h2>";
echo "<p>لاستخدام HTTP Kernel المحسن:</p>";
echo "<ol>";
echo "<li>سيتم تحديث bootstrap/app.php لاستخدام KernelFixed</li>";
echo "<li>اختبار الموقع مرة أخرى</li>";
echo "<li>إذا عمل بنجاح، يمكن إعادة تفعيل HandleCors لاحقاً</li>";
echo "</ol>";
echo "</div>";

?>
