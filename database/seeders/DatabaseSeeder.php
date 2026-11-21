<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // บัญชีทดลอง (รหัสผ่านทุกบัญชีคือ password)
        $users = [
            ['name' => 'ผู้ดูแลระบบ', 'email' => 'admin@example.com', 'role' => 'admin', 'phone' => '0800000001'],
            ['name' => 'เจ้าของร้าน', 'email' => 'owner@example.com', 'role' => 'owner', 'phone' => '0800000002'],
            ['name' => 'ลูกค้าตัวอย่าง', 'email' => 'customer@example.com', 'role' => 'customer', 'phone' => '0800000003'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                $u + ['password' => 'password']
            );
        }

        $products = [
            ['SKU-001', 'ข้าวหอมมะลิ 5 กก.', 'อาหารแห้ง', 189, 50],
            ['SKU-002', 'น้ำมันปาล์ม 1 ลิตร', 'อาหารแห้ง', 55, 80],
            ['SKU-003', 'น้ำดื่ม 600 มล. (แพ็ค 12)', 'เครื่องดื่ม', 59, 120],
            ['SKU-004', 'กาแฟสำเร็จรูป 3in1 (กล่อง)', 'เครื่องดื่ม', 129, 40],
            ['SKU-005', 'ผงซักฟอก 2 กก.', 'ของใช้ในบ้าน', 145, 30],
            ['SKU-006', 'สบู่ก้อน (แพ็ค 4)', 'ของใช้ส่วนตัว', 69, 4],
            ['SKU-007', 'แชมพู 400 มล.', 'ของใช้ส่วนตัว', 99, 25],
            ['SKU-008', 'ขนมปังโฮลวีท', 'เบเกอรี่', 45, 3],
        ];

        foreach ($products as [$sku, $name, $category, $price, $stock]) {
            Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => 'สินค้าตัวอย่างสำหรับทดลองระบบ',
                    'price' => $price,
                    'stock' => $stock,
                    'is_active' => true,
                ]
            );
        }
    }
}
