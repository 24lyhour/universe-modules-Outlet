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
            $table->foreignId('company_id')->nullable()->after('tenant_id')->constrained('companies')->nullOnDelete();
            $table->string('outlet_type')->nullable()->after('phone');
            $table->string('image_url')->nullable()->after('logo');
            $table->foreignId('created_by')->nullable()->after('schedule_status')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['company_id', 'outlet_type', 'image_url', 'created_by', 'updated_by']);
        });
    }
};
