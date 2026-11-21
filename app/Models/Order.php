<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * สถานะคำสั่งซื้อ (ขั้นตอนหลังชำระเงิน)
     * pending = รอดำเนินการ -> confirmed = ยืนยันออเดอร์ -> packing = แพ็คสินค้า -> shipped = ส่งให้ขนส่งแล้ว
     */
    public const STATUSES = [
        'pending' => ['label' => 'รอดำเนินการ', 'class' => 'warning'],
        'confirmed' => ['label' => 'ยืนยันออเดอร์แล้ว', 'class' => 'info'],
        'packing' => ['label' => 'กำลังแพ็คสินค้า', 'class' => 'teal'],
        'shipped' => ['label' => 'จัดส่งสินค้าให้กับบริการขนส่ง', 'class' => 'success'],
        'cancelled' => ['label' => 'ยกเลิก', 'class' => 'danger'],
    ];

    /** ลำดับขั้นของสถานะ (ใช้เช็กว่าอัปเดตย้อนกลับไม่ได้) */
    public const FLOW = ['pending', 'confirmed', 'packing', 'shipped'];

    /** ขั้นตอนที่ร้านค้ากดอัปเดตได้ (ใช้ในดรอปดาวน์ และข้อความบนไทม์ไลน์) */
    public const STEP_LABELS = [
        'confirmed' => 'ยืนยันออเดอร์',
        'packing' => 'แพ็คสินค้า',
        'shipped' => 'จัดส่งสินค้าให้บริการขนส่ง',
    ];

    public const PAYMENT_METHODS = [
        'bank_transfer' => ['label' => 'โอนเงินผ่านธนาคาร', 'icon' => 'bi-bank'],
        'promptpay' => ['label' => 'QR PromptPay', 'icon' => 'bi-qr-code-scan'],
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => ['label' => 'รอชำระเงิน', 'class' => 'warning'],
        'verifying' => ['label' => 'รอตรวจสอบ', 'class' => 'info'],
        'paid' => ['label' => 'ชำระแล้ว', 'class' => 'success'],
        'rejected' => ['label' => 'ไม่ผ่านการตรวจสอบ', 'class' => 'danger'],
    ];

    protected $fillable = [
        'user_id', 'order_no', 'total', 'shipping_fee', 'shipping_carrier', 'shipment_created_at', 'status', 'tracking_no',
        'payment_method', 'payment_status', 'payment_slip', 'payment_note', 'paid_at',
        'shipping_name', 'shipping_phone', 'shipping_address', 'note',
    ];

    /** ยอดค่าสินค้า (ไม่รวมค่าส่ง) — total คือยอดที่ต้องชำระ = ค่าสินค้า + ค่าส่ง */
    public function getItemsTotalAttribute(): float
    {
        return (float) $this->total - (float) $this->shipping_fee;
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'shipment_created_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusClass(): string
    {
        return self::STATUSES[$this->status]['class'] ?? 'secondary';
    }

    public function paymentMethodLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method]['label'] ?? $this->payment_method;
    }

    public function paymentMethodIcon(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method]['icon'] ?? 'bi-wallet2';
    }

    public function paymentStatusLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status]['label'] ?? $this->payment_status;
    }

    public function paymentStatusClass(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status]['class'] ?? 'secondary';
    }

    public function getSlipUrlAttribute(): ?string
    {
        return $this->payment_slip ? asset('storage/'.$this->payment_slip) : null;
    }

    /** ลูกค้ายังต้องชำระ/แจ้งชำระ (โอน หรือ PromptPay ที่ยังไม่จ่ายหรือสลิปไม่ผ่าน) */
    public function needsPayment(): bool
    {
        return in_array($this->payment_method, ['bank_transfer', 'promptpay'], true)
            && in_array($this->payment_status, ['unpaid', 'rejected'], true)
            && $this->status !== 'cancelled';
    }
}
