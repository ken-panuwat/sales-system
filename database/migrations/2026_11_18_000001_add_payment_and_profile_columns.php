<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // bank_transfer | promptpay | cod
            $table->string('payment_method', 20)->default('cod')->after('total');
            // unpaid | verifying | paid | rejected | cod
            $table->string('payment_status', 20)->default('cod')->after('payment_method');
            $table->string('payment_slip')->nullable()->after('payment_status');
            $table->string('payment_note')->nullable()->after('payment_slip');
            $table->timestamp('paid_at')->nullable()->after('payment_note');
            $table->string('tracking_no', 100)->nullable()->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method', 'payment_status', 'payment_slip',
                'payment_note', 'paid_at', 'tracking_no',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
