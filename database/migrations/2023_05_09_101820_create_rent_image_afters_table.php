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
        Schema::create('rent_image_afters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rent_order_id');
            $table->string('photo_rear')->nullable();
            $table->string('photo_front')->nullable();
            $table->string('photo_side')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_image_afters');
    }
};
