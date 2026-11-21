<?php

namespace App\Support;

/**
 * ตัวกลางการขนส่ง (ตอนนี้เป็นโหมดจำลอง)
 * เมื่อเชื่อม Flash Express จริง ให้แก้เฉพาะสองฟังก์ชันนี้ ส่วนอื่นของระบบไม่ต้องแก้
 */
class Shipping
{
    public static function carrier(): string
    {
        return (string) config('shipping.carrier');
    }

    public static function isMock(): bool
    {
        return config('shipping.driver') !== 'flash';
    }

    /**
     * คำนวณค่าส่ง
     *
     * @return array{carrier:string, fee:float, free:bool, items:int}
     */
    public static function quote(int $items, float $subtotal): array
    {
        // TODO (ของจริง): เรียก API คำนวณราคาของ Flash Express ตามน้ำหนัก/ปลายทาง

        $cfg = config('shipping');
        $fee = $cfg['base_fee'] + max(0, $items - $cfg['included_items']) * $cfg['extra_per_item'];
        $free = $cfg['free_over'] > 0 && $subtotal >= $cfg['free_over'];

        return [
            'carrier' => self::carrier(),
            'fee' => $free || $items <= 0 ? 0.0 : round($fee, 2),
            'free' => $free,
            'items' => $items,
        ];
    }

    /** สร้างรายการส่ง คืนค่าเลขพัสดุ */
    public static function createShipment(): string
    {
        // TODO (ของจริง): เรียก API สร้างพัสดุของ Flash Express แล้วคืนเลขพัสดุจริง
        return 'TH'.random_int(10000000, 99999999).random_int(100, 999).'A';
    }
}
