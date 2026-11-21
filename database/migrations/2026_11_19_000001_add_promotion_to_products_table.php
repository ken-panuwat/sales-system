<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('promo_name', 100)->nullable()->after('stock');
            // percent = ลดเป็น % / amount = ลดเป็นจำนวนเงิน (บาท)
            $table->string('discount_type', 10)->nullable()->after('promo_name');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->date('promo_start')->nullable()->after('discount_value');
            $table->date('promo_end')->nullable()->after('promo_start');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['promo_name', 'discount_type', 'discount_value', 'promo_start', 'promo_end']);
        });
    }
};
