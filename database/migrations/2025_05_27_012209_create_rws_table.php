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
        Schema::create('rws', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: RW 01
            $table->unsignedBigInteger('user_id')->nullable(); // User RW
            $table->unsignedBigInteger('daerah_id')->nullable(); // Relasi ke daerah
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('daerah_id')->references('id')->on('daerahs')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rws');
    }
};
