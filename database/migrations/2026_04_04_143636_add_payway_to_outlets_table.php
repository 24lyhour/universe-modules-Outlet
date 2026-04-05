<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->string('payway_merchant_id', 30)->nullable()->after('longitude');
            $table->text('payway_api_key')->nullable()->after('payway_merchant_id');
            $table->boolean('payway_enabled')->default(false)->after('payway_api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['payway_merchant_id', 'payway_api_key', 'payway_enabled']);
        });
    }
};
