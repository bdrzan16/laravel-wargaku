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
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: RT 01
            $table->unsignedBigInteger('user_id')->nullable(); // User RT
            $table->unsignedBigInteger('rw_id')->nullable(); // Relasi ke RW
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rw_id')->references('id')->on('rws')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rts');
    }
};
