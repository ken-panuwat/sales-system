<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_OWNER = 'owner';
    public const ROLE_CUSTOMER = 'customer';

    public const ROLES = [
        self::ROLE_ADMIN => 'ผู้ดูแลระบบ',
        self::ROLE_OWNER => 'เจ้าของร้าน',
        self::ROLE_CUSTOMER => 'ลูกค้า',
    ];

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /** ผู้ดูแลระบบหรือเจ้าของร้าน */
    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isOwner();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    /** อักษรตัวแรกของชื่อ ใช้แทนรูปโปรไฟล์เมื่อไม่ได้อัปโหลดรูป */
    public function initial(): string
    {
        return mb_strtoupper(mb_substr(trim($this->name), 0, 1)) ?: '?';
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /** ชื่อ route หน้าแรกหลังล็อกอินของแต่ละบทบาท */
    public function homeRoute(): string
    {
        return $this->isCustomer() ? 'shop.index' : 'dashboard';
    }
}
