<?php

echo "<h1>🚀 اختبار سريع للموقع</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h2>⚡ اختبار سريع</h2>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $httpKernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    
    echo "✅ Laravel يعمل بنجاح<br>";
    
    // اختبار الصفحات المهمة
    $pages = [
        '/' => 'الصفحة الرئيسية',
        '/login' => 'تسجيل الدخول',
        '/new-login' => 'تسجيل الدخول الجديد',
        '/simple-login' => 'تسجيل الدخول البسيط'
    ];
    
    echo "<h3>📄 اختبار الصفحات:</h3>";
    
    foreach ($pages as $url => $name) {
        try {
            $request = \Illuminate\Http\Request::create($url, 'GET');
            $response = $httpKernel->handle($request);
            $status = $response->getStatusCode();
            
            if ($status == 200) {
                echo "✅ $name ($url) - يعمل<br>";
            } elseif ($status == 302) {
                echo "🔄 $name ($url) - إعادة توجيه<br>";
            } else {
                echo "⚠️ $name ($url) - كود: $status<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ $name ($url) - خطأ: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في Laravel: " . $e->getMessage() . "<br>";
}

echo "</div>";

echo "<div style='background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h2>🔗 روابط سريعة</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/test-login-page.php' => '🧪 اختبار تسجيل الدخول',
    '/final-test.php' => '🎯 الاختبار النهائي',
    '/debug.php' => '🔍 التشخيص'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #ddd;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h2>📊 معلومات النظام</h2>";
echo "<p><strong>إصدار PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>الوقت:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>المنطقة الزمنية:</strong> " . date_default_timezone_get() . "</p>";
echo "</div>";

?>
