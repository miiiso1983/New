<?php

echo "<h1>🔧 إصلاح مشكلة Spatie Permission</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Spatie Permission</h2>";

echo "<p><strong>الخطأ:</strong> Trait \"Spatie\\Permission\\Traits\\HasRoles\" not found</p>";
echo "<p>هذا يعني أن مكتبة Spatie Permission غير مثبتة أو غير محملة بشكل صحيح.</p>";

// فحص وجود مكتبة Spatie
echo "<h3>📦 فحص مكتبة Spatie Permission:</h3>";

$spatieExists = class_exists('Spatie\\Permission\\Traits\\HasRoles');
if ($spatieExists) {
    echo "✅ مكتبة Spatie Permission موجودة<br>";
} else {
    echo "❌ مكتبة Spatie Permission غير موجودة<br>";
}

// فحص ملف composer.json
$composerPath = '../composer.json';
if (file_exists($composerPath)) {
    $composer = json_decode(file_get_contents($composerPath), true);
    if (isset($composer['require']['spatie/laravel-permission'])) {
        echo "✅ Spatie Permission موجودة في composer.json: " . $composer['require']['spatie/laravel-permission'] . "<br>";
    } else {
        echo "❌ Spatie Permission غير موجودة في composer.json<br>";
    }
} else {
    echo "❌ ملف composer.json غير موجود<br>";
}

// فحص vendor directory
$vendorPath = '../vendor/spatie/laravel-permission';
if (is_dir($vendorPath)) {
    echo "✅ مجلد vendor/spatie/laravel-permission موجود<br>";
} else {
    echo "❌ مجلد vendor/spatie/laravel-permission غير موجود<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ الحل المطبق</h2>";
echo "<p>تم تطبيق حل مؤقت بإزالة اعتماد User model على Spatie Permission:</p>";
echo "<ul>";
echo "<li>إزالة use Spatie\\Permission\\Traits\\HasRoles</li>";
echo "<li>إزالة HasRoles من traits المستخدمة</li>";
echo "<li>تبسيط دوال الصلاحيات لتعتمد على user_type فقط</li>";
echo "<li>إنشاء نظام صلاحيات مبسط داخلي</li>";
echo "</ul>";
echo "</div>";

try {
    echo "<h3>🚀 اختبار Laravel بدون Spatie Permission:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار User model
    try {
        $userClass = new ReflectionClass('App\\Models\\User');
        echo "✅ User model يمكن تحميله<br>";
        
        $traits = $userClass->getTraitNames();
        echo "✅ Traits المستخدمة: " . implode(', ', $traits) . "<br>";
        
        if (!in_array('Spatie\\Permission\\Traits\\HasRoles', $traits)) {
            echo "✅ تم إزالة HasRoles trait بنجاح<br>";
        } else {
            echo "❌ HasRoles trait لا يزال موجود<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ User model: " . $e->getMessage() . "<br>";
    }
    
    // اختبار إنشاء user
    try {
        $user = new \App\Models\User();
        echo "✅ يمكن إنشاء User instance<br>";
        
        // اختبار الدوال المبسطة
        $user->user_type = 'admin';
        if ($user->isAdmin()) {
            echo "✅ دالة isAdmin() تعمل<br>";
        }
        
        $permissions = $user->getAllPermissionNames();
        echo "✅ دالة getAllPermissionNames() تعمل - عدد الصلاحيات: " . count($permissions) . "<br>";
        
        if ($user->hasPermissionTo('dashboard.view')) {
            echo "✅ دالة hasPermissionTo() تعمل<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ User instance test: " . $e->getMessage() . "<br>";
    }
    
    // اختبار HTTP Kernel
    echo "<h3>🌐 اختبار HTTP Kernel:</h3>";
    
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "✅ HTTP Kernel يعمل<br>";
        
        // اختبار طلب بسيط
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        try {
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();
            echo "✅ معالجة الطلبات تعمل - كود: $status<br>";
            
            if ($status == 200) {
                echo "🎉 الصفحة الرئيسية تعمل بنجاح!<br>";
            } elseif ($status == 302) {
                $location = $response->headers->get('Location');
                echo "🔄 إعادة توجيه إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في معالجة الطلب: " . $e->getMessage() . "<br>";
            echo "الملف: " . $e->getFile() . "<br>";
            echo "السطر: " . $e->getLine() . "<br>";
        }
        
        // اختبار صفحة تسجيل الدخول
        echo "<h3>🔐 اختبار صفحة تسجيل الدخول:</h3>";
        
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        
        try {
            $loginResponse = $kernel->handle($loginRequest);
            $loginStatus = $loginResponse->getStatusCode();
            echo "✅ صفحة تسجيل الدخول - كود: $loginStatus<br>";
            
            if ($loginStatus == 200) {
                echo "🎉 صفحة تسجيل الدخول تعمل بنجاح!<br>";
            } elseif ($loginStatus == 302) {
                $location = $loginResponse->headers->get('Location');
                echo "🔄 إعادة توجيه من تسجيل الدخول إلى: $location<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ خطأ في صفحة تسجيل الدخول: " . $e->getMessage() . "<br>";
            
            if (strpos($e->getMessage(), 'Spatie') !== false || strpos($e->getMessage(), 'HasRoles') !== false) {
                echo "<strong>⚠️ لا يزال هناك مراجع لـ Spatie في مكان آخر!</strong><br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Kernel: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام في Laravel: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ نظام الصلاحيات المبسط</h2>";
echo "<p>تم إنشاء نظام صلاحيات مبسط يعتمد على user_type:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;'>";

$userTypes = [
    'admin' => [
        'الوصف' => 'مدير النظام',
        'الصلاحيات' => 'جميع الصلاحيات'
    ],
    'manager' => [
        'الوصف' => 'مدير',
        'الصلاحيات' => 'إدارة المستخدمين والطلبات والتقارير'
    ],
    'employee' => [
        'الوصف' => 'موظف',
        'الصلاحيات' => 'عرض لوحة التحكم والطلبات'
    ],
    'customer' => [
        'الوصف' => 'عميل',
        'الصلاحيات' => 'عرض وإنشاء الطلبات'
    ]
];

foreach ($userTypes as $type => $info) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<strong>$type</strong><br>";
    echo "<small style='color: #666;'>" . $info['الوصف'] . "</small><br>";
    echo "<small style='color: #666;'>" . $info['الصلاحيات'] . "</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إصلاح مشكلة Spatie Permission، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/nuclear-fix.php' => '☢️ الحل النووي'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
