# 🚀 دليل تثبيت نظام إدارة الصيدلية على BlueHost

## 📋 المتطلبات:
- استضافة BlueHost (Choice Plus أو أعلى)
- PHP 8.1 أو أحدث
- MySQL 5.7 أو أحدث
- نطاق مربوط بالاستضافة
- SSL Certificate (مجاني مع BlueHost)

## 🔧 خطوات التثبيت:

### 1. رفع الملفات:
1. في cPanel، اذهب لـ File Manager
2. ارفع جميع ملفات المشروع لمجلد `public_html`
3. تأكد من رفع المجلدات المخفية (مثل .htaccess)
4. انسخ محتوى `.htaccess.bluehost` لملف `.htaccess` في المجلد الرئيسي

### 2. إعداد قاعدة البيانات:
1. في cPanel، اذهب لـ: MySQL Databases
2. أنشئ قاعدة بيانات جديدة:
   ```
   Database Name: username_pharmacy
   ```
3. أنشئ مستخدم جديد:
   ```
   Username: username_pharmacy
   Password: [كلمة مرور قوية]
   ```
4. اربط المستخدم بقاعدة البيانات مع جميع الصلاحيات

### 3. إعداد ملف البيئة:
1. انسخ محتوى `.env.bluehost` إلى ملف جديد اسمه `.env`
2. عدّل بيانات قاعدة البيانات:
   ```
   DB_DATABASE=username_pharmacy
   DB_USERNAME=username_pharmacy
   DB_PASSWORD=your_password
   ```
3. عدّل رابط الموقع:
   ```
   APP_URL=https://yourpharmacy.com
   ```
4. عدّل إعدادات البريد الإلكتروني:
   ```
   MAIL_HOST=mail.yourpharmacy.com
   MAIL_USERNAME=admin@yourpharmacy.com
   MAIL_PASSWORD=your_email_password
   ```

### 4. تشغيل الإعداد التلقائي:
1. اذهب لـ: `https://yoursite.com/bluehost-setup.php`
2. انتظر اكتمال جميع الخطوات
3. احفظ بيانات تسجيل الدخول
4. احذف الملف فوراً

### 5. إعداد Document Root (اختياري):
إذا كنت تريد أن يعمل الموقع من المجلد الرئيسي مباشرة:
1. في cPanel، اذهب لـ: Subdomains
2. غيّر Document Root إلى: `public_html/public`

### 6. إعداد SSL:
1. في cPanel، اذهب لـ: SSL/TLS
2. فعّل Let's Encrypt SSL (مجاني)
3. فعّل Force HTTPS Redirect

### 7. اختبار النظام:
1. اذهب لموقعك: `https://yoursite.com`
2. سجل دخولك بالبيانات:
   - البريد: admin@pharmacy.com
   - كلمة المرور: admin123
3. غيّر كلمة المرور فوراً

## 🔒 إعدادات الأمان:

### ملفات يجب حذفها بعد التثبيت:
- `bluehost-setup.php`
- `BLUEHOST_INSTALLATION.md` (هذا الملف)
- أي ملفات إعداد أخرى

### صلاحيات المجلدات:
- storage: 755
- bootstrap/cache: 755
- public: 755

### إعدادات إضافية:
1. فعّل Cloudflare (مجاني مع BlueHost)
2. إعداد النسخ الاحتياطية التلقائية
3. مراقبة الأداء

## 🛠️ حل المشاكل:

### مشكلة 500 Internal Server Error:
1. تحقق من ملف .env
2. تحقق من صلاحيات المجلدات
3. راجع error logs في cPanel

### مشكلة Database Connection:
1. تحقق من بيانات قاعدة البيانات في .env
2. تأكد من إنشاء قاعدة البيانات في cPanel
3. تأكد من ربط المستخدم بقاعدة البيانات

### مشكلة File Permissions:
1. في File Manager، غيّر صلاحيات storage إلى 755
2. غيّر صلاحيات bootstrap/cache إلى 755

### مشكلة SSL:
1. تأكد من تفعيل SSL في cPanel
2. انتظر حتى 24 ساعة لانتشار الشهادة
3. فعّل Force HTTPS

## 📈 تحسين الأداء:

### 1. تفعيل OPcache:
في cPanel → PHP Configuration → فعّل OPcache

### 2. تحسين قاعدة البيانات:
```sql
OPTIMIZE TABLE table_name;
```

### 3. استخدام CDN:
فعّل Cloudflare من خلال BlueHost

## 📞 الدعم:
- دعم BlueHost: 24/7 Live Chat
- دعم فني: support@bluehost.com
- قاعدة المعرفة: help.bluehost.com

## 🎉 تهانينا!
تم تثبيت نظام إدارة الصيدلية بنجاح على BlueHost!

### الخطوات التالية:
1. إعداد النسخ الاحتياطية
2. تخصيص إعدادات النظام
3. إضافة المستخدمين والمنتجات
4. اختبار جميع الوظائف
