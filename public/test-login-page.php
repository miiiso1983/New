<?php

echo "<h1>🔐 اختبار صفحة تسجيل الدخول</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚀 اختبار Laravel وصفحة تسجيل الدخول</h2>";

try {
    echo "<p>📁 تحميل Laravel...</p>";
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel بنجاح<br>";
    
    // اختبار HTTP Kernel
    $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel يعمل<br>";
    
    // اختبار طلب صفحة تسجيل الدخول
    echo "<p>🔐 اختبار صفحة تسجيل الدخول:</p>";
    
    $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
    $loginRequest->headers->set('Accept', 'text/html');
    
    try {
        $response = $httpKernel->handle($loginRequest);
        $statusCode = $response->getStatusCode();
        
        echo "كود الاستجابة: $statusCode<br>";
        
        if ($statusCode == 200) {
            echo "✅ صفحة تسجيل الدخول تعمل بنجاح!<br>";
            
            // فحص محتوى الصفحة
            $content = $response->getContent();
            if (strpos($content, 'login') !== false || strpos($content, 'تسجيل') !== false) {
                echo "✅ محتوى صفحة تسجيل الدخول موجود<br>";
            } else {
                echo "⚠️ محتوى صفحة تسجيل الدخول قد يكون مفقود<br>";
            }
            
        } elseif ($statusCode == 302) {
            $location = $response->headers->get('Location');
            echo "🔄 إعادة توجيه إلى: $location<br>";
            
        } elseif ($statusCode == 404) {
            echo "❌ صفحة تسجيل الدخول غير موجودة (404)<br>";
            
        } else {
            echo "⚠️ كود استجابة غير متوقع: $statusCode<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ خطأ في معالجة طلب تسجيل الدخول: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
    }
    
    // اختبار المسارات
    echo "<p>🛣️ فحص المسارات:</p>";
    
    try {
        $router = $app->make('router');
        $routes = $router->getRoutes();
        
        $loginRoutes = [];
        foreach ($routes as $route) {
            $uri = $route->uri();
            if (strpos($uri, 'login') !== false) {
                $loginRoutes[] = $uri . ' (' . implode('|', $route->methods()) . ')';
            }
        }
        
        if (!empty($loginRoutes)) {
            echo "✅ مسارات تسجيل الدخول الموجودة:<br>";
            foreach ($loginRoutes as $route) {
                echo "- $route<br>";
            }
        } else {
            echo "⚠️ لم يتم العثور على مسارات تسجيل الدخول<br>";
        }
        
    } catch (Exception $e) {
        echo "⚠️ لا يمكن فحص المسارات: " . $e->getMessage() . "<br>";
    }
    
    // اختبار قاعدة البيانات
    echo "<p>🗄️ اختبار قاعدة البيانات:</p>";
    
    try {
        $db = $app->make('db');
        $users = $db->table('users')->count();
        echo "✅ قاعدة البيانات تعمل - عدد المستخدمين: $users<br>";
        
        if ($users == 0) {
            echo "⚠️ لا يوجد مستخدمين في قاعدة البيانات<br>";
            echo "يمكنك إنشاء مستخدم تجريبي من خلال تشغيل السيدر<br>";
        }
        
    } catch (Exception $e) {
        echo "⚠️ مشكلة في قاعدة البيانات: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 روابط الاختبار</h2>";
echo "<p>جرب هذه الروابط:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank' style='color: #155724;'>الصفحة الرئيسية</a></li>";
echo "<li><a href='/login' target='_blank' style='color: #155724;'>تسجيل الدخول</a></li>";
echo "<li><a href='/new-login' target='_blank' style='color: #155724;'>تسجيل الدخول الجديد</a></li>";
echo "<li><a href='/simple-login' target='_blank' style='color: #155724;'>تسجيل الدخول البسيط</a></li>";
echo "<li><a href='/debug-login' target='_blank' style='color: #155724;'>تشخيص تسجيل الدخول</a></li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📋 معلومات إضافية</h2>";
echo "<p><strong>ملفات التشخيص الأخرى:</strong></p>";
echo "<ul>";
echo "<li><a href='/final-test.php' target='_blank'>الاختبار النهائي</a></li>";
echo "<li><a href='/fix-trait-error.php' target='_blank'>إصلاح مشاكل Trait</a></li>";
echo "<li><a href='/debug.php' target='_blank'>التشخيص الشامل</a></li>";
echo "</ul>";
echo "</div>";

?>
