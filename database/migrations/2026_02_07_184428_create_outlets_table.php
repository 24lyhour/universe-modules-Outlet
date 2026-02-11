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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tenant_type')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->unsignedBigInteger('menu_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('google_map_url')->nullable();
            $table->string('url_deeplink')->nullable();
            $table->string('status')->default('active');
            $table->string('schedule_mode')->nullable();
            $table->json('schedule_days')->nullable();
            $table->time('schedule_start_time')->nullable();
            $table->time('schedule_end_time')->nullable();
            $table->date('schedule_start_date')->nullable();
            $table->date('schedule_end_date')->nullable();
            $table->string('schedule_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
