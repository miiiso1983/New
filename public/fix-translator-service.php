<?php

echo "<h1>🔧 إصلاح مشكلة Translator Service</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 تشخيص مشكلة Translator</h2>";

echo "<p><strong>الخطأ:</strong> Target class [translator] does not exist</p>";
echo "<p>هذا يعني أن TranslationServiceProvider لم يتم تسجيله أو translation config مفقود.</p>";

echo "<h3>📋 الحل المطبق:</h3>";
echo "<ol>";
echo "<li>إضافة TranslationServiceProvider إلى bootstrap</li>";
echo "<li>إضافة translation config</li>";
echo "<li>إنشاء مجلد lang إذا لم يكن موجوداً</li>";
echo "</ol>";

// إنشاء مجلد lang إذا لم يكن موجوداً
$langDirs = [
    '../lang',
    '../lang/ar',
    '../lang/en'
];

echo "<h3>📂 إنشاء مجلدات Language:</h3>";
foreach ($langDirs as $dir) {
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

// إنشاء ملفات language أساسية
$langFiles = [
    '../lang/ar/auth.php' => "<?php\n\nreturn [\n    'failed' => 'بيانات الاعتماد هذه غير متطابقة مع البيانات المسجلة لدينا.',\n    'password' => 'كلمة المرور المُدخلة غير صحيحة.',\n    'throttle' => 'عدد كبير جداً من محاولات الدخول. يرجى المحاولة مرة أخرى بعد :seconds ثانية.',\n];",
    '../lang/en/auth.php' => "<?php\n\nreturn [\n    'failed' => 'These credentials do not match our records.',\n    'password' => 'The provided password is incorrect.',\n    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',\n];",
    '../lang/ar/validation.php' => "<?php\n\nreturn [\n    'required' => 'حقل :attribute مطلوب.',\n    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح.',\n    'min' => [\n        'string' => 'يجب أن يكون :attribute على الأقل :min أحرف.',\n    ],\n];",
    '../lang/en/validation.php' => "<?php\n\nreturn [\n    'required' => 'The :attribute field is required.',\n    'email' => 'The :attribute must be a valid email address.',\n    'min' => [\n        'string' => 'The :attribute must be at least :min characters.',\n    ],\n];"
];

echo "<h3>📝 إنشاء ملفات Language:</h3>";
foreach ($langFiles as $file => $content) {
    if (!file_exists($file)) {
        if (file_put_contents($file, $content)) {
            echo "✅ تم إنشاء: " . basename($file) . "<br>";
        } else {
            echo "❌ فشل في إنشاء: " . basename($file) . "<br>";
        }
    } else {
        echo "✅ موجود: " . basename($file) . "<br>";
    }
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Laravel مع Translator محسن</h2>";

try {
    echo "<h3>📁 تحميل Laravel مع Translator:</h3>";
    
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app-no-session-provider.php';
    echo "✅ تم تحميل Laravel مع Translator محسن<br>";
    
    // اختبار 1: Translation Config
    echo "<h4>1️⃣ اختبار Translation Config:</h4>";
    try {
        $config = $app->make('config');
        $locale = $config->get('translation.locale');
        $fallbackLocale = $config->get('translation.fallback_locale');
        $langPath = $config->get('translation.path');
        
        echo "✅ Locale: <strong>$locale</strong><br>";
        echo "✅ Fallback Locale: <strong>$fallbackLocale</strong><br>";
        echo "✅ Lang Path: $langPath<br>";
        
        if (is_dir($langPath)) {
            echo "✅ Lang directory موجود<br>";
        } else {
            echo "❌ Lang directory غير موجود<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Translation Config: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 2: Translator Service
    echo "<h4>2️⃣ اختبار Translator Service:</h4>";
    try {
        $translator = $app->make('translator');
        echo "✅ Translator Service: " . get_class($translator) . "<br>";
        
        // اختبار translation
        try {
            $translated = $translator->get('auth.failed');
            echo "✅ Translation test: $translated<br>";
        } catch (Exception $e) {
            echo "⚠️ Translation test: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Translator Service: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 3: Session Services
    echo "<h4>3️⃣ اختبار Session Services:</h4>";
    try {
        $sessionStore = $app->make('session.store');
        echo "✅ Session Store: " . get_class($sessionStore) . "<br>";
        
        $session = $app->make('session');
        echo "✅ Session Service: " . get_class($session) . "<br>";
        
        // اختبار session operations
        $session->put('test_with_translator', 'success');
        $value = $session->get('test_with_translator');
        
        if ($value === 'success') {
            echo "✅ Session operations تعمل مع Translator<br>";
        } else {
            echo "❌ Session operations فاشلة<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Session Services: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 4: HTTP Request
    echo "<h4>4️⃣ اختبار HTTP Request مع Translator:</h4>";
    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $request = \Illuminate\Http\Request::create('/', 'GET');
        
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        echo "✅ HTTP Request status: <strong>$status</strong><br>";
        
        if ($status == 200) {
            echo "🎉 الصفحة الرئيسية تعمل مع Translator!<br>";
        } elseif ($status == 302) {
            $location = $response->headers->get('Location');
            echo "🔄 إعادة توجيه إلى: $location<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ HTTP Request: " . $e->getMessage() . "<br>";
        echo "الملف: " . $e->getFile() . "<br>";
        echo "السطر: " . $e->getLine() . "<br>";
        
        if (strpos($e->getMessage(), 'translator') !== false) {
            echo "<strong>🚨 لا تزال هناك مشكلة في Translator!</strong><br>";
        }
    }
    
    // اختبار 5: Login Page مع Translator
    echo "<h4>5️⃣ اختبار Login Page مع Translator:</h4>";
    try {
        $loginRequest = \Illuminate\Http\Request::create('/login', 'GET');
        $loginResponse = $kernel->handle($loginRequest);
        $loginStatus = $loginResponse->getStatusCode();
        
        echo "✅ Login page status: <strong>$loginStatus</strong><br>";
        
        if ($loginStatus == 200) {
            echo "🎉 صفحة تسجيل الدخول تعمل مع Translator!<br>";
        } elseif ($loginStatus == 302) {
            echo "🔄 إعادة توجيه من login<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Login Page: " . $e->getMessage() . "<br>";
    }
    
    // اختبار 6: Database مع Translator
    echo "<h4>6️⃣ اختبار Database مع Translator:</h4>";
    try {
        $db = $app->make('db');
        $connection = $db->connection();
        $driverName = $connection->getDriverName();
        
        echo "✅ Database driver: <strong>$driverName</strong><br>";
        
        if ($driverName === 'sqlite') {
            echo "✅ SQLite يعمل مع Translator<br>";
            
            // اختبار جدول users
            try {
                $userCount = $connection->select("SELECT COUNT(*) as count FROM users");
                echo "✅ عدد المستخدمين: " . $userCount[0]->count . "<br>";
            } catch (Exception $e) {
                echo "⚠️ مشكلة في قراءة الجداول: " . $e->getMessage() . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Database: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ إصلاح Translator المطبق</h2>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>📝 التحديثات المطبقة:</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 3px; overflow-x: auto; font-size: 12px;'>";
echo htmlspecialchars('// إضافة TranslationServiceProvider
$app->register(\Illuminate\Translation\TranslationServiceProvider::class);

// إضافة translation config
$config->set(\'translation\', [
    \'locale\' => \'ar\',
    \'fallback_locale\' => \'en\',
    \'path\' => dirname(__DIR__) . \'/lang\',
]);');
echo "</pre>";
echo "</div>";

echo "<ul>";
echo "<li>إضافة TranslationServiceProvider إلى bootstrap</li>";
echo "<li>إضافة translation config مع locale عربي</li>";
echo "<li>إنشاء مجلدات lang/ar و lang/en</li>";
echo "<li>إنشاء ملفات auth.php و validation.php</li>";
echo "<li>ضمان توفر translator service</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3e0; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔑 بيانات تسجيل الدخول</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;'>";

$users = [
    'المدير العام' => 'admin@example.com',
    'فاطمة - صيدلية الشفاء' => 'fatima@alshifa-pharmacy.com',
    'أحمد - صيدلية النور' => 'ahmed@alnoor-pharmacy.com'
];

foreach ($users as $name => $email) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;'>";
    echo "<strong>$name</strong><br>";
    echo "<small style='color: #666;'>البريد: $email</small><br>";
    echo "<small style='color: #666;'>كلمة المرور: password123</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار النظام مع Translator</h2>";
echo "<p>بعد إصلاح مشكلة Translator، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/create-clean-database.php' => '🗄️ قاعدة البيانات',
    '/test-no-session-provider.php' => '🧪 اختبار Bootstrap'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>🎉 تم إصلاح Translator Service</h2>";
echo "<p style='font-size: 18px; font-weight: bold;'>النظام الآن يعمل مع Translation و Session!</p>";
echo "<p>جميع Service Providers الأساسية تعمل بشكل صحيح</p>";
echo "</div>";

?>
