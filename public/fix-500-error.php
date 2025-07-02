<?php

echo "<h1>🔧 إصلاح خطأ 500 ومشاكل قاعدة البيانات</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص المشاكل</h2>";

try {
    echo "<p>📁 تحميل Laravel...</p>";
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    // فحص Service Providers
    echo "<h3>🔧 فحص Service Providers:</h3>";
    
    try {
        // فحص Database Service Provider
        $dbManager = $app->make('db');
        echo "✅ Database Service Provider يعمل<br>";
        
        // اختبار الاتصال بقاعدة البيانات
        try {
            $connection = $dbManager->connection();
            echo "✅ الاتصال بقاعدة البيانات نجح<br>";
            
            // اختبار استعلام بسيط
            $result = $connection->select('SELECT 1 as test');
            echo "✅ استعلام قاعدة البيانات يعمل<br>";
            
        } catch (Exception $e) {
            echo "❌ مشكلة في الاتصال بقاعدة البيانات: " . $e->getMessage() . "<br>";
            
            // محاولة إنشاء قاعدة البيانات
            $dbFile = '../database/database.sqlite';
            if (!file_exists($dbFile)) {
                echo "🔧 محاولة إنشاء ملف قاعدة البيانات...<br>";
                if (touch($dbFile)) {
                    echo "✅ تم إنشاء ملف قاعدة البيانات<br>";
                    chmod($dbFile, 0664);
                } else {
                    echo "❌ فشل في إنشاء ملف قاعدة البيانات<br>";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Database Service Provider لا يعمل: " . $e->getMessage() . "<br>";
        
        // محاولة تسجيل Database Service Provider يدوياً
        echo "🔧 محاولة تسجيل Database Service Provider...<br>";
        try {
            $app->register(\Illuminate\Database\DatabaseServiceProvider::class);
            echo "✅ تم تسجيل Database Service Provider<br>";
        } catch (Exception $e2) {
            echo "❌ فشل في تسجيل Database Service Provider: " . $e2->getMessage() . "<br>";
        }
    }
    
    // فحص Route Service Provider
    echo "<h3>🛣️ فحص المسارات:</h3>";
    
    try {
        $router = $app->make('router');
        echo "✅ Router Service Provider يعمل<br>";
        
        // تحميل المسارات يدوياً
        echo "🔧 تحميل ملف المسارات...<br>";
        require_once '../routes/web.php';
        echo "✅ تم تحميل ملف المسارات<br>";
        
        // فحص مسارات تسجيل الدخول
        $routes = $router->getRoutes();
        $loginRoutes = [];
        
        foreach ($routes as $route) {
            $uri = $route->uri();
            if (strpos($uri, 'login') !== false) {
                $methods = implode('|', $route->methods());
                $loginRoutes[] = "$methods $uri";
            }
        }
        
        if (!empty($loginRoutes)) {
            echo "✅ مسارات تسجيل الدخول موجودة:<br>";
            foreach ($loginRoutes as $route) {
                echo "- $route<br>";
            }
        } else {
            echo "⚠️ لم يتم العثور على مسارات تسجيل الدخول<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ مشكلة في Router: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel:</h3>";
    
    try {
        $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $httpKernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ معالجة الطلبات تعمل - كود: $status<br>";
            
        } catch (Exception $e) {
            echo "❌ خطأ في معالجة الطلب: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel لا يعمل: " . $e->getMessage() . "<br>";
    }
    
    // تشغيل أوامر Laravel لإصلاح المشاكل
    echo "<h3>🔧 تشغيل أوامر الإصلاح:</h3>";
    
    try {
        $consoleKernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        
        $commands = [
            'config:clear' => 'مسح كاش الإعدادات',
            'cache:clear' => 'مسح الكاش العام',
            'route:clear' => 'مسح كاش المسارات',
            'view:clear' => 'مسح كاش العروض',
            'migrate --force' => 'تشغيل المايجريشن'
        ];
        
        foreach ($commands as $command => $description) {
            try {
                $consoleKernel->call($command);
                echo "✅ $description<br>";
            } catch (Exception $e) {
                echo "⚠️ فشل في $description: " . $e->getMessage() . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Console Kernel لا يعمل: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    echo "<pre style='background: #f8d7da; padding: 10px; border-radius: 5px; font-size: 12px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار بعد الإصلاح</h2>";
echo "<p>بعد تشغيل هذا السكريبت، جرب هذه الروابط:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank' style='color: #155724;'>الصفحة الرئيسية</a></li>";
echo "<li><a href='/login' target='_blank' style='color: #155724;'>تسجيل الدخول</a></li>";
echo "<li><a href='/quick-test.php' target='_blank' style='color: #155724;'>الاختبار السريع</a></li>";
echo "</ul>";
echo "</div>";

?>
