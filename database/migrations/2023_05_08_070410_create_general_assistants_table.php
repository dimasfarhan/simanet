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
        Schema::create('general_assistants', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->uuid('uuid');
            $table->string('name');
            $table->string('nip');
            $table->string('phone');
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->enum('gender', ['MALE', 'FEMALE'])->default('MALE');
            $table->enum('religion', ['ISLAM', 'KRISTEN', 'KHATOLIK', 'BUDHA', 'HINDU', 'KONG_WU_CHU'])->default('ISLAM');
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->date('date_joined');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_assistants');
    }
};
