<?php

echo "<h1>🗄️ إنشاء قاعدة البيانات المنظمة</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 إنشاء قاعدة بيانات مبسطة ومنظمة</h2>";
echo "<p>سيتم إنشاء الجداول الأساسية فقط للنظام</p>";

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
    
    // حذف الجداول الموجودة إذا كانت موجودة
    echo "<h3>🗑️ حذف الجداول القديمة:</h3>";
    $tables = ['users', 'customers', 'items', 'orders', 'order_items', 'invoices', 'personal_access_tokens', 'sessions', 'password_reset_tokens'];
    
    foreach ($tables as $table) {
        try {
            $connection->statement("DROP TABLE IF EXISTS {$table}");
            echo "✅ تم حذف جدول {$table}<br>";
        } catch (Exception $e) {
            echo "⚠️ لم يتم العثور على جدول {$table}<br>";
        }
    }
    
    // إنشاء جدول users
    echo "<h3>👥 إنشاء جدول Users:</h3>";
    
    $usersTable = "
    CREATE TABLE users (
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
    
    // إنشاء جدول customers
    echo "<h3>🏢 إنشاء جدول Customers:</h3>";
    
    $customersTable = "
    CREATE TABLE customers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NULL,
        phone VARCHAR(255) NULL,
        address TEXT NULL,
        company_name VARCHAR(255) NULL,
        tax_number VARCHAR(255) NULL,
        customer_code VARCHAR(100) UNIQUE NULL,
        status VARCHAR(50) DEFAULT 'active',
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $connection->statement($customersTable);
    echo "✅ تم إنشاء جدول customers<br>";
    
    // إنشاء جدول items
    echo "<h3>📦 إنشاء جدول Items:</h3>";
    
    $itemsTable = "
    CREATE TABLE items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        code VARCHAR(100) UNIQUE NULL,
        barcode VARCHAR(255) NULL,
        description TEXT NULL,
        price DECIMAL(10,2) DEFAULT 0,
        cost DECIMAL(10,2) DEFAULT 0,
        stock_quantity INTEGER DEFAULT 0,
        min_stock INTEGER DEFAULT 0,
        unit VARCHAR(50) DEFAULT 'piece',
        category VARCHAR(100) NULL,
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $connection->statement($itemsTable);
    echo "✅ تم إنشاء جدول items<br>";
    
    // إنشاء جدول orders
    echo "<h3>📋 إنشاء جدول Orders:</h3>";
    
    $ordersTable = "
    CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number VARCHAR(100) UNIQUE NOT NULL,
        customer_id INTEGER NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        total_amount DECIMAL(10,2) DEFAULT 0,
        discount DECIMAL(10,2) DEFAULT 0,
        tax DECIMAL(10,2) DEFAULT 0,
        notes TEXT NULL,
        order_date DATE DEFAULT CURRENT_DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    )";
    
    $connection->statement($ordersTable);
    echo "✅ تم إنشاء جدول orders<br>";
    
    // إنشاء جدول order_items
    echo "<h3>📝 إنشاء جدول Order Items:</h3>";
    
    $orderItemsTable = "
    CREATE TABLE order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        item_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES items(id)
    )";
    
    $connection->statement($orderItemsTable);
    echo "✅ تم إنشاء جدول order_items<br>";
    
    // إنشاء جدول invoices
    echo "<h3>🧾 إنشاء جدول Invoices:</h3>";
    
    $invoicesTable = "
    CREATE TABLE invoices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        invoice_number VARCHAR(100) UNIQUE NOT NULL,
        customer_id INTEGER NOT NULL,
        order_id INTEGER NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        paid_amount DECIMAL(10,2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'pending',
        due_date DATE NULL,
        invoice_date DATE DEFAULT CURRENT_DATE,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )";
    
    $connection->statement($invoicesTable);
    echo "✅ تم إنشاء جدول invoices<br>";
    
    // إنشاء جدول sessions
    echo "<h3>📝 إنشاء جدول Sessions:</h3>";
    
    $sessionsTable = "
    CREATE TABLE sessions (
        id VARCHAR(255) PRIMARY KEY,
        user_id INTEGER NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        payload TEXT NOT NULL,
        last_activity INTEGER NOT NULL
    )";
    
    $connection->statement($sessionsTable);
    echo "✅ تم إنشاء جدول sessions<br>";
    
    // إنشاء جدول password_reset_tokens
    echo "<h3>🔑 إنشاء جدول Password Reset Tokens:</h3>";
    
    $passwordResetTable = "
    CREATE TABLE password_reset_tokens (
        email VARCHAR(255) PRIMARY KEY,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $connection->statement($passwordResetTable);
    echo "✅ تم إنشاء جدول password_reset_tokens<br>";
    
    // إنشاء جدول personal_access_tokens
    echo "<h3>🔐 إنشاء جدول Personal Access Tokens:</h3>";
    
    $tokensTable = "
    CREATE TABLE personal_access_tokens (
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
    
    // إنشاء بيانات تجريبية
    echo "<h3>👤 إنشاء بيانات تجريبية:</h3>";
    
    // إنشاء مستخدمين
    $users = [
        ['مدير النظام', 'admin@example.com', 'admin'],
        ['فاطمة', 'fatima@alshifa-pharmacy.com', 'admin'],
        ['أحمد', 'ahmed@alnoor-pharmacy.com', 'manager'],
        ['سارة', 'sara@customer.com', 'customer']
    ];
    
    foreach ($users as $user) {
        $connection->statement("
            INSERT OR IGNORE INTO users (name, email, password, user_type, status) 
            VALUES (?, ?, ?, ?, ?)
        ", [
            $user[0],
            $user[1],
            password_hash('password123', PASSWORD_DEFAULT),
            $user[2],
            'active'
        ]);
    }
    echo "✅ تم إنشاء " . count($users) . " مستخدم<br>";
    
    // إنشاء عملاء
    $customers = [
        ['صيدلية الشفاء', 'info@alshifa.com', '07701234567', 'بغداد - الكرادة'],
        ['صيدلية النور', 'info@alnoor.com', '07709876543', 'بصرة - المعقل'],
        ['صيدلية الأمل', 'info@alamal.com', '07705555555', 'أربيل - المركز']
    ];
    
    foreach ($customers as $customer) {
        $connection->statement("
            INSERT OR IGNORE INTO customers (name, email, phone, address, status) 
            VALUES (?, ?, ?, ?, ?)
        ", [
            $customer[0],
            $customer[1],
            $customer[2],
            $customer[3],
            'active'
        ]);
    }
    echo "✅ تم إنشاء " . count($customers) . " عميل<br>";
    
    // إنشاء منتجات
    $items = [
        ['باراسيتامول 500mg', 'PARA500', '1000', 'أقراص مسكنة للألم', 250, 200],
        ['أموكسيسيلين 250mg', 'AMOX250', '2000', 'مضاد حيوي', 500, 400],
        ['فيتامين سي 1000mg', 'VITC1000', '3000', 'مكمل غذائي', 150, 100],
        ['أسبرين 100mg', 'ASP100', '4000', 'مضاد للتجلط', 300, 250]
    ];
    
    foreach ($items as $item) {
        $connection->statement("
            INSERT OR IGNORE INTO items (name, code, barcode, description, price, cost, stock_quantity, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $item[0],
            $item[1],
            $item[2],
            $item[3],
            $item[4],
            $item[5],
            100,
            'active'
        ]);
    }
    echo "✅ تم إنشاء " . count($items) . " منتج<br>";
    
    // فحص الجداول المنشأة
    echo "<h3>📊 فحص الجداول المنشأة:</h3>";
    
    $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    echo "✅ الجداول الموجودة:<br>";
    foreach ($tables as $table) {
        $count = $connection->select("SELECT COUNT(*) as count FROM {$table->name}");
        echo "  - {$table->name}: " . $count[0]->count . " سجل<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في إنشاء قاعدة البيانات: " . $e->getMessage() . "<br>";
    echo "الملف: " . $e->getFile() . "<br>";
    echo "السطر: " . $e->getLine() . "<br>";
}

echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ قاعدة البيانات المنظمة</h2>";
echo "<ul>";
echo "<li><strong>users</strong> - المستخدمين والإداريين</li>";
echo "<li><strong>customers</strong> - العملاء</li>";
echo "<li><strong>items</strong> - المنتجات والأدوية</li>";
echo "<li><strong>orders</strong> - الطلبات</li>";
echo "<li><strong>order_items</strong> - تفاصيل الطلبات</li>";
echo "<li><strong>invoices</strong> - الفواتير</li>";
echo "<li><strong>sessions</strong> - جلسات المستخدمين</li>";
echo "<li><strong>password_reset_tokens</strong> - رموز إعادة تعيين كلمة المرور</li>";
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
    ],
    'أحمد - صيدلية النور' => [
        'البريد' => 'ahmed@alnoor-pharmacy.com',
        'كلمة المرور' => 'password123',
        'النوع' => 'manager'
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
echo "<h2>🎯 اختبار النظام</h2>";
echo "<p>بعد إنشاء قاعدة البيانات المنظمة، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/dashboard' => '📊 لوحة التحكم',
    '/users' => '👥 المستخدمين',
    '/customers' => '🏢 العملاء',
    '/items' => '📦 المنتجات',
    '/orders' => '📋 الطلبات',
    '/invoices' => '🧾 الفواتير'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
