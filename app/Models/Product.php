<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku', 'name', 'category', 'description',
        'price', 'stock', 'image', 'is_active',
        'promo_name', 'discount_type', 'discount_value', 'promo_start', 'promo_end',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'discount_value' => 'decimal:2',
            'promo_start' => 'date',
            'promo_end' => 'date',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    /** โปรโมชันกำลังใช้งานอยู่หรือไม่ (มีส่วนลด และวันนี้อยู่ในช่วงวันที่ที่กำหนด) */
    public function getIsOnSaleAttribute(): bool
    {
        if (! $this->discount_type || (float) $this->discount_value <= 0) {
            return false;
        }

        $today = today();

        if ($this->promo_start && $today->lt($this->promo_start)) {
            return false;
        }

        if ($this->promo_end && $today->gt($this->promo_end)) {
            return false;
        }

        return true;
    }

    /** ราคาที่ขายจริง (หลังหักส่วนลด ถ้ามีโปรโมชัน) */
    public function getFinalPriceAttribute(): float
    {
        $price = (float) $this->price;

        if (! $this->is_on_sale) {
            return $price;
        }

        $value = (float) $this->discount_value;

        $final = $this->discount_type === 'percent'
            ? $price * (1 - min($value, 100) / 100)
            : $price - $value;

        return round(max($final, 0), 2);
    }

    /** เปอร์เซ็นต์ที่ลดจริง ใช้แสดงป้าย -20% */
    public function getDiscountPercentAttribute(): int
    {
        $price = (float) $this->price;

        return $price > 0 ? (int) round((1 - $this->final_price / $price) * 100) : 0;
    }

    /** เฉพาะสินค้าที่โปรโมชันกำลังใช้งานอยู่ */
    public function scopeOnSale($query)
    {
        $today = today();

        return $query->whereNotNull('discount_type')
            ->where('discount_value', '>', 0)
            ->where(fn ($q) => $q->whereNull('promo_start')->orWhereDate('promo_start', '<=', $today))
            ->where(fn ($q) => $q->whereNull('promo_end')->orWhereDate('promo_end', '>=', $today));
    }

    /**
     * รหัสสินค้าถัดไป: รันต่อจากรหัสตัวล่าสุด เช่น SKU-009 -> SKU-010
     * (ถ้ายังไม่มีสินค้า เริ่มที่ SKU-001)
     */
    public static function nextSku(): string
    {
        $latest = static::where('sku', 'like', 'SKU-%')
            ->orderByRaw('LENGTH(sku) DESC')
            ->orderByDesc('sku')
            ->value('sku');

        $number = ($latest && preg_match('/(\d+)$/', $latest, $m)) ? ((int) $m[1]) + 1 : 1;

        return 'SKU-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('category', 'like', "%{$term}%");
            });
        }

        return $query;
    }
}
