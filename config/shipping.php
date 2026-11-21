<?php

/*
 * การขนส่ง — ตอนนี้เป็นโหมดจำลอง (mock) ยังไม่เชื่อมระบบ Flash Express จริง
 * เมื่อลูกค้านำไปใช้จริง ให้เปลี่ยน driver เป็น 'flash' และเขียนการเรียก API ใน app/Support/Shipping.php
 * (ฟังก์ชัน quote() = คำนวณค่าส่ง, createShipment() = สร้างพัสดุ/เลขพัสดุ)
 */
return [
    'driver' => env('SHIPPING_DRIVER', 'mock'),
    'carrier' => env('SHIPPING_CARRIER', 'Flash Express'),

    // อัตราค่าส่งจำลอง: ค่าส่งเริ่มต้น ครอบคลุมสินค้า N ชิ้นแรก ชิ้นถัดไปคิดเพิ่มชิ้นละ X บาท
    'base_fee' => (float) env('SHIPPING_BASE_FEE', 38),
    'included_items' => (int) env('SHIPPING_INCLUDED_ITEMS', 3),
    'extra_per_item' => (float) env('SHIPPING_EXTRA_PER_ITEM', 10),

    // ส่งฟรีเมื่อยอดสินค้า (หลังหักโปรโมชัน) ถึงจำนวนนี้ ตั้งเป็น 0 = ไม่มีส่งฟรี
    'free_over' => (float) env('SHIPPING_FREE_OVER', 500),

    // ข้อมูลผู้ส่ง (พิมพ์บนใบปะหน้า)
    'sender' => [
        'name' => env('SHOP_SENDER_NAME', 'ร้านค้าสีเขียว'),
        'phone' => env('SHOP_SENDER_PHONE', '0800000000'),
        'address' => env('SHOP_SENDER_ADDRESS', '123 ถนนตัวอย่าง แขวง/ตำบล เขต/อำเภอ จังหวัด 10000'),
    ],
];
