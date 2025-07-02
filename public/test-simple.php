<?php

echo "<h1>🧪 اختبار بسيط جداً</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚀 اختبار Laravel المبسط</h2>";

try {
    echo "<p>📁 تحميل Composer...</p>";
    require_once '../vendor/autoload.php';
    echo "✅ Composer تم تحميله<br>";
    
    echo "<p>🏗️ تحميل Laravel...</p>";
    $app = require_once '../bootstrap/app.php';
    echo "✅ Laravel تم تحميله<br>";
    
    echo "<p>🔧 فحص config service...</p>";
    try {
        $config = $app->make('config');
        echo "✅ Config service يعمل<br>";
        echo "نوع Config: " . get_class($config) . "<br>";
        
        // اختبار قراءة إعداد
        $appName = $config->get('app.name', 'غير محدد');
        echo "✅ اسم التطبيق: $appName<br>";
        
    } catch (Exception $e) {
        echo "❌ Config service لا يعمل: " . $e->getMessage() . "<br>";
    }
    
    echo "<p>🌐 اختبار HTTP Kernel...</p>";
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط جداً
        echo "<p>📄 اختبار طلب بسيط...</p>";
        $request = \Illuminate\Http\Request::create('/', 'GET');
        echo "✅ Request تم إنشاؤه<br>";
        
        // محاولة معالجة الطلب
        try {
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
            
        } catch (Exception $e) {
            echo "❌ خطأ في معالجة الطلب: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel لا يعمل: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
    
    if (strpos($e->getMessage(), 'config') !== false) {
        echo "<br><strong>🔧 تم تطبيق bootstrap مبسط:</strong><br>";
        echo "- إنشاء config repository مباشرة<br>";
        echo "- تحميل الإعدادات الأساسية يدوياً<br>";
        echo "- تجنب Service Providers المعقدة<br>";
    }
}

echo "</div>";

echo "<div style='background: #f0f9ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔗 اختبار الصفحات</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/new-login' => '🆕 تسجيل الدخول الجديد',
    '/simple-login' => '🔑 تسجيل الدخول البسيط'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #3b82f6; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>📊 معلومات النظام</h2>";
echo "<p><strong>إصدار PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>الذاكرة المستخدمة:</strong> " . round(memory_get_usage() / 1024 / 1024, 2) . " MB</p>";
echo "<p><strong>الوقت:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "</div>";

?>
