# 🏥 نظام ERP لإدارة المذاخر - Laravel Backend

تطبيق ويب متكامل مطور بـ Laravel لإدارة مخازن الأدوية مع دعم كامل لتصدير التقارير بصيغة PDF و Excel وواجهات API للتطبيقات المحمولة.

## 🚀 المميزات الرئيسية

### 💻 تطبيق الويب
- **لوحة تحكم العملاء**: استعراض الطلبات والفواتير وإعادة الطلب والمرتجعات
- **لوحة تحكم المدير العام**: إدارة شاملة للنظام والمستخدمين والتقارير المالية
- **إدارة العناصر**: تعريف وإدارة الأدوية والمخزون
- **إدارة الموردين**: إنشاء وتعديل بيانات الموردين
- **نظام الفواتير والتحصيلات**: إدارة كاملة للمدفوعات
- **التقارير المالية**: تقارير شاملة قابلة للتصدير

### 🔗 واجهات API
- **RESTful API**: واجهات برمجية شاملة للتطبيقات المحمولة
- **Laravel Sanctum**: نظام مصادقة آمن
- **تصدير التقارير**: PDF و Excel عبر API
- **إدارة الأدوار**: نظام صلاحيات متقدم

## 🛠️ التقنيات المستخدمة

- **Backend**: PHP Laravel 12
- **Database**: SQLite (قابل للتغيير إلى MySQL)
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **PDF Export**: DomPDF
- **Excel Export**: Maatwebsite Excel
- **API**: RESTful API

## 📋 متطلبات النظام

- PHP 8.2+
- Composer
- SQLite أو MySQL 8.0+
- Node.js & NPM (اختياري)

## 🔧 التثبيت والإعداد

### 1. تثبيت التبعيات
```bash
composer install
```

### 2. إعداد البيئة
```bash
# نسخ ملف البيئة (موجود مسبقاً)
cp .env.example .env

# إنشاء مفتاح التطبيق
php artisan key:generate
```

### 3. إعداد قاعدة البيانات
```bash
# تشغيل الـ migrations
php artisan migrate

# تشغيل الـ seeders لإنشاء البيانات الأساسية
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 4. تشغيل الخادم
```bash
php artisan serve --host=0.0.0.0 --port=8001
```

## 👥 المستخدمون الافتراضيون

| النوع | البريد الإلكتروني | كلمة المرور | الصلاحيات |
|-------|------------------|-------------|-----------|
| مدير عام | admin@pharmacy-erp.com | password123 | جميع الصلاحيات |
| موظف | employee@pharmacy-erp.com | password123 | صلاحيات محدودة |
| عميل | customer@pharmacy-erp.com | password123 | صلاحيات العميل |

## 📊 التقارير المتاحة

جميع التقارير قابلة للتصدير بصيغة **PDF** و **Excel**:

### 1. تقرير الطلبات
- فلترة حسب التاريخ والحالة والعميل
- عرض تفاصيل كاملة للطلبات
- إحصائيات الطلبات

### 2. تقرير الفواتير
- حالة الدفع (مدفوعة، معلقة، متأخرة)
- المبالغ المدفوعة والمتبقية
- تواريخ الاستحقاق

### 3. تقرير التحصيلات
- طرق الدفع المختلفة
- تفاصيل المحصل والتواريخ
- إجمالي التحصيلات

### 4. التقرير المالي الشامل
- ملخص مالي شامل
- نسب الدفع والتحصيل
- إحصائيات متقدمة

## 🔗 API Endpoints

### المصادقة
```
POST /api/auth/register - تسجيل مستخدم جديد
POST /api/auth/login - تسجيل الدخول
POST /api/auth/logout - تسجيل الخروج
GET /api/auth/profile - عرض الملف الشخصي
PUT /api/auth/profile - تحديث الملف الشخصي
```

### الطلبات
```
GET /api/orders - عرض الطلبات
POST /api/orders - إنشاء طلب جديد
GET /api/orders/{id} - عرض طلب محدد
PUT /api/orders/{id}/status - تحديث حالة الطلب
POST /api/orders/{id}/repeat - إعادة طلب
```

### التقارير
```
GET /api/reports/dashboard-stats - إحصائيات الداشبورد
GET /api/reports/orders?format=excel|pdf - تقرير الطلبات
GET /api/reports/invoices?format=excel|pdf - تقرير الفواتير
GET /api/reports/collections?format=excel|pdf - تقرير التحصيلات
GET /api/reports/financial?format=excel|pdf - التقرير المالي
```

## 🗄️ هيكل قاعدة البيانات

### الجداول الرئيسية
- `users` - المستخدمون
- `suppliers` - الموردون
- `items` - العناصر/الأدوية
- `orders` - الطلبات
- `order_items` - عناصر الطلبات
- `invoices` - الفواتير
- `collections` - التحصيلات
- `returns` - المرتجعات
- `roles` & `permissions` - الأدوار والصلاحيات

## 🔒 الأمان

- **Laravel Sanctum** للمصادقة في API
- **Spatie Permission** لإدارة الأدوار والصلاحيات
- **تشفير كلمات المرور** باستخدام bcrypt
- **حماية CSRF** للنماذج
- **تنظيف البيانات** والتحقق من صحتها

## 🚀 النشر

### تحسين للإنتاج
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### Docker
```bash
docker-compose up -d
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed --class=RolesAndPermissionsSeeder
```

## 📁 الملفات المهمة

- `app/Models/` - نماذج قاعدة البيانات
- `app/Http/Controllers/Api/` - متحكمات API
- `app/Exports/` - فئات تصدير Excel
- `resources/views/reports/` - قوالب PDF
- `database/migrations/` - ملفات الهجرة
- `database/seeders/` - ملفات البذر
- `routes/api.php` - مسارات API

## 📞 الدعم والمساعدة

للحصول على المساعدة أو الإبلاغ عن مشاكل، يرجى مراجعة الوثائق أو التواصل مع فريق التطوير.

---

**تم تطوير هذا النظام باستخدام أحدث التقنيات لضمان الأداء والأمان والموثوقية.**
