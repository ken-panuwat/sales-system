<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) ลบ "เก็บเงินปลายทาง (COD)" ออกจากข้อมูลเดิม
        //    - ออเดอร์ที่จัดส่ง/สำเร็จไปแล้ว ถือว่าได้รับเงินแล้ว
        DB::table('orders')
            ->where('payment_status', 'cod')
            ->whereIn('status', ['shipped', 'completed'])
            ->update([
                'payment_status' => 'paid',
                'paid_at' => DB::raw('COALESCE(paid_at, updated_at)'),
            ]);

        //    - ออเดอร์ที่ยังไม่จัดส่ง กลับไปเป็น "รอชำระเงิน"
        DB::table('orders')->where('payment_status', 'cod')->update(['payment_status' => 'unpaid']);

        //    - วิธีชำระเงิน cod -> โอนผ่านธนาคาร
        DB::table('orders')->where('payment_method', 'cod')->update(['payment_method' => 'bank_transfer']);

        // 2) ปรับสถานะเดิมให้ตรงกับขั้นตอนใหม่
        DB::table('orders')->where('status', 'processing')->update(['status' => 'confirmed']);
        DB::table('orders')->where('status', 'completed')->update(['status' => 'shipped']);

        // 3) ค่าเริ่มต้นของคอลัมน์ ไม่ใช่ cod อีกต่อไป
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 20)->default('bank_transfer')->change();
            $table->string('payment_status', 20)->default('unpaid')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 20)->default('cod')->change();
            $table->string('payment_status', 20)->default('cod')->change();
        });
    }
};
