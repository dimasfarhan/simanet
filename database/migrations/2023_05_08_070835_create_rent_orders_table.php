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
        Schema::create('rent_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->uuid('staff_uuid');
            $table->uuid('ga_uuid')->nullable();
            $table->dateTime('ga_approved_at')->nullable();
            $table->uuid('bod_uuid')->nullable();
            $table->dateTime('bod_approved_at')->nullable();
            $table->enum('status', [
                'SUBMITTED', 'WAITING_FOR_APPROVAL', 'APPROVED_BY_GA', 'APPROVED',
                'REJECT', 'DONE'
            ])->default('SUBMITTED');
            $table->dateTime('rented_at');
            $table->dateTime('returned_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_orders');
    }
};
