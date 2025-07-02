<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// use Spatie\Permission\Traits\HasRoles; // معطل مؤقتاً
use App\Traits\TenantScoped;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, TenantScoped; // إزالة HasRoles مؤقتاً

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'company_name',
        'tax_number',
        'user_type',
        'status',
        'notes',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // العلاقات
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function createdOrders()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'customer_id');
    }

    public function collectedPayments()
    {
        return $this->hasMany(Collection::class, 'collected_by');
    }

    public function returns()
    {
        return $this->hasMany(ReturnOrder::class, 'customer_id');
    }

    public function processedReturns()
    {
        return $this->hasMany(ReturnOrder::class, 'processed_by');
    }

    // دوال مساعدة للصلاحيات (مبسطة بدون Spatie)
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isManager(): bool
    {
        return $this->user_type === 'manager';
    }

    public function isEmployee(): bool
    {
        return $this->user_type === 'employee';
    }

    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
    }

    // دالة للحصول على جميع الصلاحيات (مبسطة)
    public function getAllPermissionNames(): array
    {
        return $this->getLegacyPermissions();
    }

    // دالة للتحقق من صلاحية معينة (مبسطة)
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // تجاهل $guardName في النسخة المبسطة
        return $this->hasLegacyPermission($permission);
    }

    // دالة مساعدة للحصول على الصلاحيات
    private function getLegacyPermissions(): array
    {
        return match($this->user_type) {
            'admin' => [
                'dashboard.view', 'users.view', 'users.create', 'users.edit', 'users.delete',
                'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
                'orders.view', 'orders.create', 'orders.edit', 'orders.delete',
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                'reports.view', 'settings.view', 'settings.edit'
            ],
            'manager' => [
                'dashboard.view', 'users.view', 'employees.view',
                'orders.view', 'orders.create', 'orders.edit',
                'invoices.view', 'invoices.create', 'reports.view'
            ],
            'employee' => [
                'dashboard.view', 'orders.view', 'items.view'
            ],
            'customer' => [
                'orders.view', 'orders.create', 'invoices.view'
            ],
            default => []
        };
    }

    // دالة للتوافق مع النظام القديم
    private function hasLegacyPermission($permission): bool
    {
        return match($this->user_type) {
            'admin' => true, // المدير له جميع الصلاحيات
            'manager' => in_array($permission, [
                'dashboard.view', 'users.view', 'employees.view',
                'orders.view', 'orders.create', 'orders.edit',
                'invoices.view', 'invoices.create', 'reports.view'
            ]),
            'employee' => in_array($permission, [
                'dashboard.view', 'orders.view', 'items.view'
            ]),
            'customer' => in_array($permission, [
                'orders.view', 'orders.create', 'invoices.view'
            ]),
            default => false
        };
    }
}
