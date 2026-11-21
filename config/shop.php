<?php

/*
 * ข้อมูลรับชำระเงินของร้าน — แก้ได้ที่ไฟล์ .env (ถ้าไม่ตั้งจะใช้ค่าตัวอย่างด้านล่าง)
 * ข้อความภาษาไทยใน .env ต้องใส่เครื่องหมายคำพูด เช่น SHOP_BANK_NAME="ธนาคารกสิกรไทย"
 */
return [
    'bank' => [
        'name' => env('SHOP_BANK_NAME', 'ธนาคารกสิกรไทย'),
        'account_no' => env('SHOP_BANK_ACCOUNT_NO', '123-4-56789-0'),
        'account_name' => env('SHOP_BANK_ACCOUNT_NAME', 'ร้านค้าสีเขียว'),
    ],

    // เบอร์โทร 10 หลัก หรือเลขบัตรประชาชน/เลขผู้เสียภาษี 13 หลัก ที่ผูกกับ PromptPay
    'promptpay_id' => env('SHOP_PROMPTPAY_ID', '0800000002'),
    'promptpay_name' => env('SHOP_PROMPTPAY_NAME', 'ร้านค้าสีเขียว'),
];
