<?php

echo "<h1>☢️ الحل النووي - إزالة جميع ملفات Config</h1>";

// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
echo "<h2>⚠️ تحذير: الحل النووي</h2>";
echo "<p>هذا الحل سيحذف جميع ملفات config ويعيد إنشاءها بإعدادات SQLite فقط</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 المشكلة المكتشفة</h2>";
echo "<p>ملفات config موجودة وتتجاهل إعدادات bootstrap:</p>";
echo "<ul>";
echo "<li>config/cache.php يستخدم 'database' كافتراضي</li>";
echo "<li>config/session.php يستخدم 'database' كافتراضي</li>";
echo "<li>config/database.php قد يحتوي على إعدادات MySQL</li>";
echo "</ul>";

// نسخ احتياطي من ملفات config
$configBackupDir = '../config-backup-' . date('Y-m-d-H-i-s');
if (!is_dir($configBackupDir)) {
    mkdir($configBackupDir, 0755, true);
    echo "✅ تم إنشاء مجلد النسخ الاحتياطي: $configBackupDir<br>";
}

// نسخ ملفات config للنسخ الاحتياطي
$configFiles = glob('../config/*.php');
foreach ($configFiles as $file) {
    $filename = basename($file);
    if (copy($file, "$configBackupDir/$filename")) {
        echo "✅ تم نسخ: $filename<br>";
    }
}

echo "</div>";

echo "<div style='background: #f8d7da; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>💣 تطبيق الحل النووي</h2>";

// حذف ملفات config المشكلة
$problematicConfigs = [
    '../config/cache.php',
    '../config/session.php',
    '../config/database.php',
    '../config/queue.php'
];

foreach ($problematicConfigs as $configFile) {
    if (file_exists($configFile)) {
        if (unlink($configFile)) {
            echo "💥 تم حذف: " . basename($configFile) . "<br>";
        } else {
            echo "❌ فشل في حذف: " . basename($configFile) . "<br>";
        }
    } else {
        echo "ℹ️ غير موجود: " . basename($configFile) . "<br>";
    }
}

// إنشاء config جديدة بإعدادات SQLite فقط
echo "<h3>🔧 إنشاء ملفات config جديدة:</h3>";

// إنشاء database.php جديد
$databaseConfig = '<?php

return [
    "default" => "sqlite",
    "connections" => [
        "sqlite" => [
            "driver" => "sqlite",
            "database" => "/home/1486247.cloudwaysapps.com/tvhxmzcvgt/public_html/database/database.sqlite",
            "prefix" => "",
            "foreign_key_constraints" => true,
        ],
    ],
    "migrations" => "migrations",
    "redis" => [
        "client" => "phpredis",
        "options" => [
            "cluster" => "redis",
            "prefix" => "laravel_database_",
        ],
        "default" => [
            "url" => null,
            "host" => "127.0.0.1",
            "password" => null,
            "port" => "6379",
            "database" => "0",
        ],
        "cache" => [
            "url" => null,
            "host" => "127.0.0.1",
            "password" => null,
            "port" => "6379",
            "database" => "1",
        ],
    ],
];';

if (file_put_contents('../config/database.php', $databaseConfig)) {
    echo "✅ تم إنشاء database.php جديد<br>";
} else {
    echo "❌ فشل في إنشاء database.php<br>";
}

// إنشاء cache.php جديد
$cacheConfig = '<?php

return [
    "default" => "file",
    "stores" => [
        "array" => [
            "driver" => "array",
            "serialize" => false,
        ],
        "file" => [
            "driver" => "file",
            "path" => storage_path("framework/cache/data"),
        ],
    ],
    "prefix" => "laravel_cache",
];';

if (file_put_contents('../config/cache.php', $cacheConfig)) {
    echo "✅ تم إنشاء cache.php جديد<br>";
} else {
    echo "❌ فشل في إنشاء cache.php<br>";
}

// إنشاء session.php جديد
$sessionConfig = '<?php

return [
    "driver" => "file",
    "lifetime" => 120,
    "expire_on_close" => false,
    "encrypt" => false,
    "files" => storage_path("framework/sessions"),
    "connection" => null,
    "table" => "sessions",
    "store" => null,
    "lottery" => [2, 100],
    "cookie" => "laravel_session",
    "path" => "/",
    "domain" => null,
    "secure" => null,
    "http_only" => true,
    "same_site" => "lax",
    "partitioned" => false,
];';

if (file_put_contents('../config/session.php', $sessionConfig)) {
    echo "✅ تم إنشاء session.php جديد<br>";
} else {
    echo "❌ فشل في إنشاء session.php<br>";
}

// إنشاء queue.php جديد
$queueConfig = '<?php

return [
    "default" => "sync",
    "connections" => [
        "sync" => [
            "driver" => "sync",
        ],
    ],
    "batching" => [
        "database" => "sqlite",
        "table" => "job_batches",
    ],
    "failed" => [
        "driver" => "file",
        "path" => storage_path("logs/failed-jobs.log"),
    ],
];';

if (file_put_contents('../config/queue.php', $queueConfig)) {
    echo "✅ تم إنشاء queue.php جديد<br>";
} else {
    echo "❌ فشل في إنشاء queue.php<br>";
}

echo "</div>";

// اختبار Laravel مع config الجديدة
echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 اختبار Laravel مع Config الجديدة</h2>";

try {
    require_once '../vendor/autoload.php';
    echo "✅ تم تحميل Composer<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ تم تحميل Laravel<br>";
    
    // اختبار config
    $config = $app->make('config');
    echo "✅ Config service يعمل<br>";
    
    $dbDefault = $config->get('database.default');
    echo "✅ Database default: <strong>$dbDefault</strong><br>";
    
    $cacheDefault = $config->get('cache.default');
    echo "✅ Cache default: <strong>$cacheDefault</strong><br>";
    
    $sessionDriver = $config->get('session.driver');
    echo "✅ Session driver: <strong>$sessionDriver</strong><br>";
    
    // اختبار database
    try {
        $db = $app->make('db');
        $connection = $db->connection();
        $driverName = $connection->getDriverName();
        echo "✅ Database driver: <strong>$driverName</strong><br>";
        
        if ($driverName === 'sqlite') {
            echo "🎉 تأكيد: يستخدم SQLite!<br>";
        } else {
            echo "❌ لا يزال يستخدم: $driverName<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database test: " . $e->getMessage() . "<br>";
    }
    
    // اختبار cache
    try {
        $cache = $app->make('cache');
        $cache->put('nuclear_test', 'success', 60);
        $value = $cache->get('nuclear_test');
        if ($value === 'success') {
            echo "✅ Cache (file) يعمل بنجاح<br>";
        }
        $cache->forget('nuclear_test');
        
    } catch (Exception $e) {
        echo "❌ Cache test: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel test: " . $e->getMessage() . "<br>";
}

echo "</div>";

echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 اختبار الموقع الآن</h2>";
echo "<p>بعد الحل النووي، جرب الروابط التالية:</p>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;'>";

$links = [
    '/' => '🏠 الصفحة الرئيسية',
    '/login' => '🔐 تسجيل الدخول',
    '/test-sqlite-only.php' => '🧪 اختبار SQLite',
    '/ultimate-fix.php' => '🚀 الحل الشامل'
];

foreach ($links as $url => $title) {
    echo "<a href='$url' target='_blank' style='display: block; padding: 10px; background: white; border-radius: 5px; text-decoration: none; color: #333; border: 1px solid #007bff; text-align: center;'>$title</a>";
}

echo "</div>";
echo "</div>";

?>
