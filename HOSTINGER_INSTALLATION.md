# 🚀 دليل تثبيت نظام إدارة الصيدلية على Hostinger

## 📋 المتطلبات:
- استضافة Hostinger (Business أو أعلى)
- PHP 8.1 أو أحدث
- MySQL 5.7 أو أحدث
- نطاق مربوط بالاستضافة

## 🔧 خطوات التثبيت:

### 1. رفع الملفات:
1. ارفع جميع ملفات المشروع لمجلد `public_html`
2. تأكد من رفع المجلدات المخفية (مثل .htaccess)

### 2. إعداد قاعدة البيانات:
1. في hPanel، اذهب لـ: Databases → MySQL Databases
2. أنشئ قاعدة بيانات جديدة
3. احفظ بيانات الاتصال

### 3. إعداد ملف البيئة:
1. انسخ محتوى `.env.hostinger` إلى ملف جديد اسمه `.env`
2. عدّل بيانات قاعدة البيانات:
   ```
   DB_DATABASE=u123456789_pharmacy
   DB_USERNAME=u123456789_pharmacy
   DB_PASSWORD=your_password
   ```
3. عدّل رابط الموقع:
   ```
   APP_URL=https://yourpharmacy.com
   ```

### 4. توليد مفتاح التطبيق:
1. اذهب لـ: `https://yoursite.com/generate-key.php`
2. انتظر اكتمال العملية
3. احذف الملف فوراً

### 5. إعداد قاعدة البيانات:
1. اذهب لـ: `https://yoursite.com/migrate.php`
2. انتظر اكتمال إنشاء الجداول
3. احذف الملف فوراً

### 6. إنشاء مستخدم إداري:
1. اذهب لـ: `https://yoursite.com/create-admin.php`
2. احفظ بيانات تسجيل الدخول
3. احذف الملف فوراً

### 7. إعداد Document Root:
1. في hPanel، اذهب لـ: Advanced → PHP Configuration
2. غيّر Document Root إلى: `public_html/public`
3. احفظ التغييرات

### 8. اختبار النظام:
1. اذهب لموقعك: `https://yoursite.com`
2. سجل دخولك بالبيانات:
   - البريد: admin@pharmacy.com
   - كلمة المرور: admin123
3. غيّر كلمة المرور فوراً

## 🔒 إعدادات الأمان:

### ملفات يجب حذفها بعد التثبيت:
- `generate-key.php`
- `migrate.php`
- `create-admin.php`
- `HOSTINGER_INSTALLATION.md` (هذا الملف)

### صلاحيات المجلدات:
- storage: 755
- bootstrap/cache: 755
- public: 755

## 🛠️ حل المشاكل:

### مشكلة 500 Internal Server Error:
1. تحقق من ملف .env
2. تحقق من صلاحيات المجلدات
3. تحقق من error logs في hPanel

### مشكلة Database Connection:
1. تحقق من بيانات قاعدة البيانات في .env
2. تأكد من إنشاء قاعدة البيانات في hPanel
3. تأكد من صحة اسم المستخدم وكلمة المرور

### مشكلة File Permissions:
1. في File Manager، غيّر صلاحيات storage إلى 755
2. غيّر صلاحيات bootstrap/cache إلى 755

## 📞 الدعم:
إذا واجهت أي مشاكل، تواصل مع دعم Hostinger أو راجع الوثائق.

## 🎉 تهانينا!
تم تثبيت نظام إدارة الصيدلية بنجاح على Hostinger!
