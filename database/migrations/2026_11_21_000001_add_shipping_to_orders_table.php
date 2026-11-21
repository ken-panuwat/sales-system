<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('total');
            $table->string('shipping_carrier', 50)->nullable()->after('shipping_fee');
            $table->timestamp('shipment_created_at')->nullable()->after('tracking_no');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_fee', 'shipping_carrier', 'shipment_created_at']);
        });
    }
};
