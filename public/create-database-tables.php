<?php

echo "<h1>🗄️ إنشاء جداول قاعدة البيانات</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 مشكلة الجداول المفقودة</h2>";

echo "<p><strong>الخطأ:</strong> SQLSTATE[HY000]: General error: 1 no such table: users</p>";
echo "<p>هذا يعني أن قاعدة البيانات SQLite فارغة ولا تحتوي على الجداول المطلوبة.</p>";

// إنشاء ملف قاعدة البيانات إذا لم يكن موجوداً
$dbPath = '/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite';
$dbDir = dirname($dbPath);

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
    echo "✅ تم إنشاء مجلد database<br>";
}

if (!file_exists($dbPath)) {
    touch($dbPath);
    chmod($dbPath, 0664);
    echo "✅ تم إنشاء ملف SQLite<br>";
} else {
    echo "✅ ملف SQLite موجود<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🛠️ إنشاء الجداول الأساسية</h2>";

try {
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // الحصول على اتصال قاعدة البيانات
    $db = $app->make('db');
    $connection = $db->connection();
    
    echo "✅ تم الاتصال بقاعدة البيانات SQLite<br>";
    
    // إنشاء جدول users
    echo "<h3>👥 إنشاء جدول Users:</h3>";
    
    $usersTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        email_verified_at TIMESTAMP NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(255) NULL,
        address TEXT NULL,
        company_name VARCHAR(255) NULL,
        tax_number VARCHAR(255) NULL,
        user_type VARCHAR(50) DEFAULT 'customer',
        status VARCHAR(50) DEFAULT 'active',
        notes TEXT NULL,
        locale VARCHAR(10) DEFAULT 'ar',
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $connection->statement($usersTable);
    echo "✅ تم إنشاء جدول users<br>";
    
    // إنشاء جدول password_reset_tokens
    echo "<h3>🔑 إنشاء جدول Password Reset Tokens:</h3>";
    
    $passwordResetTable = "
    CREATE TABLE IF NOT EXISTS password_reset_tokens (
        email VARCHAR(255) PRIMARY KEY,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $connection->statement($passwordResetTable);
    echo "✅ تم إنشاء جدول password_reset_tokens<br>";
    
    // إنشاء جدول sessions
    echo "<h3>📝 إنشاء جدول Sessions:</h3>";
    
    $sessionsTable = "
    CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(255) PRIMARY KEY,
        user_id INTEGER NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        payload TEXT NOT NULL,
        last_activity INTEGER NOT NULL
    )";
    
    $connection->statement($sessionsTable);
    echo "✅ تم إنشاء جدول sessions<br>";
    
    // إنشاء جدول personal_access_tokens
    echo "<h3>🔐 إنشاء جدول Personal Access Tokens:</h3>";
    
    $tokensTable = "
    CREATE TABLE IF NOT EXISTS personal_access_tokens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tokenable_type VARCHAR(255) NOT NULL,
        tokenable_id INTEGER NOT NULL,
        name VARCHAR(255) NOT NULL,
        token VARCHAR(64) UNIQUE NOT NULL,
        abilities TEXT NULL,
        last_used_at TIMESTAMP NULL,
        expires_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $connection->statement($tokensTable);
    echo "✅ تم إنشاء جدول personal_access_tokens<br>";
    
    // إنشاء مستخدم تجريبي
    echo "<h3>👤 إنشاء مستخدم تجريبي:</h3>";
    
    $userExists = $connection->select("SELECT COUNT(*) as count FROM users WHERE email = 'admin@example.com'");
    
    if ($userExists[0]->count == 0) {
        $connection->statement("
            INSERT INTO users (name, email, password, user_type, status) 
            VALUES (?, ?, ?, ?, ?)
        ", [
            'مدير النظام',
            'admin@example.com',
            bcrypt('password123'),
            'admin',
            'active'
        ]);
        echo "✅ تم إنشاء مستخدم تجريبي: admin@example.com / password123<br>";
    } else {
        echo "ℹ️ المستخدم التجريبي موجود مسبقاً<br>";
    }
    
    // إنشاء المستخدم المطلوب
    $targetEmail = 'fatima@alshifa-pharmacy.com';
    $targetUserExists = $connection->select("SELECT COUNT(*) as count FROM users WHERE email = ?", [$targetEmail]);
    
    if ($targetUserExists[0]->count == 0) {
        $connection->statement("
            INSERT INTO users (name, email, password, user_type, status, company_name) 
            VALUES (?, ?, ?, ?, ?, ?)
        ", [
            'فاطمة',
            $targetEmail,
            bcrypt('password123'),
            'admin',
            'active',
            'صيدلية الشفاء'
        ]);
        echo "✅ تم إنشاء المستخدم: $targetEmail / password123<br>";
    } else {
        echo "ℹ️ المستخدم $targetEmail موجود مسبقاً<br>";
    }
    
    // فحص الجداول المنشأة
    echo "<h3>📊 فحص الجداول المنشأة:</h3>";
    
    $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "✅ الجداول الموجودة:<br>";
    foreach ($tables as $table) {
        $count = $connection->select("SELECT COUNT(*) as count FROM {$table->name}");
        echo "  - {$table->name}: " . $count[0]->count . " سجل<br>";
    }
    
    // اختبار تسجيل الدخول
    echo "<h3>🔐 اختبار تسجيل الدخول:</h3>";
    
    $testUser = $connection->select("SELECT * FROM users WHERE email = ? LIMIT 1", [$targetEmail]);
    if (!empty($testUser)) {
        echo "✅ يمكن العثور على المستخدم: " . $testUser[0]->name . "<br>";
        echo "✅ نوع المستخدم: " . $testUser[0]->user_type . "<br>";
        echo "✅ حالة المستخدم: " . $testUser[0]->status . "<br>";
    } else {
        echo "❌ لا يمكن العثور على المستخدم<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في إنشاء الجداول: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ الجداول المنشأة</h2>";
echo "<ul>";
echo "<li><strong>users</strong> - جدول المستخدمين الأساسي</li>";
echo "<li><strong>password_reset_tokens</strong> - رموز إعادة تعيين كلمة المرور</li>";
echo "<li><strong>sessions</strong> - جلسات المستخدمين</li>";
echo "<li><strong>personal_access_tokens</strong> - رموز الوصول الشخصية</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3e0; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>👤 بيانات تسجيل الدخول</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;'>";

$loginCredentials = [
    'المدير العام' => [
        'البريد' => 'admin@example.com',
        'كلمة المرور' => 'password123',
        'النوع' => 'admin'
    ],
    'فاطمة - صيدلية الشفاء' => [
        'البريد' => 'fatima@alshifa-pharmacy.com',
        'كلمة المرور' => 'password123',
        'النوع' => 'admin'
    ]
];

foreach ($loginCredentials as $title => $creds) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;'>";
    echo "<strong>$title</strong><br>";
    echo "<small style='color: #666;'>البريد: " . $creds['البريد'] . "</small><br>";
    echo "<small style='color: #666;'>كلمة المرور: " . $creds['كلمة المرور'] . "</small><br>";
    echo "<small style='color: #666;'>النوع: " . $creds['النوع'] . "</small>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد إنشاء الجداول والمستخدمين، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/register' => '📝 التسجيل'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
