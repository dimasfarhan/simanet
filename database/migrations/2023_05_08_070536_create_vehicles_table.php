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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kendaraan');
            $table->string('nomor_polisi');
            $table->string('brand');
            $table->string('model_type');
            $table->enum('status', ['AVAILABLE', 'UNAVAILABLE', 'RENTED', 'MAINTENANCE'])->default('AVAILABLE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
