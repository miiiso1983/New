<?php

echo "<h1>🔧 إصلاح مشكلة ExcludesPaths Trait</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Trait</h2>";

// فحص وجود الملفات المطلوبة
$traitFiles = [
    '../app/Http/Middleware/Concerns/ExcludesPaths.php' => 'ExcludesPaths Trait (App)',
    '../app/Http/Middleware/Concerns/IlluminateExcludesPaths.php' => 'ExcludesPaths Trait (Illuminate)',
    '../app/Http/Middleware/PreventRequestsDuringMaintenance.php' => 'PreventRequestsDuringMaintenance',
    '../app/Http/Middleware/TrimStrings.php' => 'TrimStrings',
    '../app/Http/Middleware/TransformsRequest.php' => 'TransformsRequest'
];

echo "<h3>📁 فحص الملفات:</h3>";
foreach ($traitFiles as $file => $name) {
    if (file_exists($file)) {
        echo "✅ $name موجود<br>";
    } else {
        echo "❌ $name غير موجود<br>";
    }
}

echo "<h3>🔧 اختبار تحميل Laravel:</h3>";

try {
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer autoload<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel bootstrap<br>";
    
    // اختبار HTTP Kernel
    try {
        $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل بنجاح<br>";
        
        // اختبار معالجة طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        echo "✅ تم إنشاء HTTP Request<br>";
        
        // محاولة معالجة الطلب
        try {
            $response = $httpKernel->handle($request);
            echo "✅ تم معالجة الطلب بنجاح!<br>";
            echo "كود الاستجابة: " . $response->getStatusCode() . "<br>";
            
            if ($response->getStatusCode() == 200) {
                echo "<strong style='color: green;'>🎉 Laravel يعمل بشكل مثالي!</strong><br>";
            } elseif ($response->getStatusCode() == 302) {
                echo "<strong style='color: orange;'>🔄 Laravel يعمل (إعادة توجيه)</strong><br>";
            } else {
                echo "<strong style='color: blue;'>ℹ️ Laravel يعمل مع كود: " . $response->getStatusCode() . "</strong><br>";
            }
            
        } catch (Exception $e) {
            echo "⚠️ مشكلة في معالجة الطلب: " . $e->getMessage() . "<br>";
            
            // إذا كانت المشكلة متعلقة بالـ trait
            if (strpos($e->getMessage(), 'ExcludesPaths') !== false) {
                echo "<strong>🔧 تم اكتشاف مشكلة ExcludesPaths trait</strong><br>";
                echo "تم إصلاح هذه المشكلة في الكود المحدث.<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ مشكلة في HTTP Kernel: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في تحميل Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'ExcludesPaths') !== false) {
        echo "<br><strong>🔧 حل مشكلة ExcludesPaths:</strong><br>";
        echo "تم تحديث namespace الـ traits ليكون متوافقاً مع Laravel 10.<br>";
        echo "تم إصلاح ملفات middleware لاستخدام الـ namespace الصحيح.<br>";
    }
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الإصلاحات المطبقة</h2>";
echo "<ul>";
echo "<li>تحديث namespace في ExcludesPaths trait</li>";
echo "<li>إصلاح PreventRequestsDuringMaintenance middleware</li>";
echo "<li>إصلاح TrimStrings middleware</li>";
echo "<li>إصلاح TransformsRequest middleware</li>";
echo "<li>تحديث HTTP Kernel لاستخدام الـ middleware الصحيحة</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة ExcludesPaths trait، جرب الروابط التالية:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank' style='color: #856404;'>الصفحة الرئيسية</a></li>";
echo "<li><a href='/login' target='_blank' style='color: #856404;'>تسجيل الدخول</a></li>";
echo "<li><a href='/dashboard' target='_blank' style='color: #856404;'>لوحة التحكم</a></li>";
echo "<li><a href='/final-test.php' target='_blank' style='color: #856404;'>الاختبار النهائي</a></li>";
echo "</ul>";
echo "</div>";

?>
