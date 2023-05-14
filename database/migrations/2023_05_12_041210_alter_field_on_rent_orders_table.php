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
        Schema::table('rent_orders', function (Blueprint $table) {
            $table->uuid('user_uuid')->nullable()->after('id');
            $table->dropColumn('staff_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_orders', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
            $table->uuid('staff_uuid');
        });
    }
};
