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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name'); //Nama User
            $table->string('email')->unique(); //Email User
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->unsignedBigInteger('rw_id')->nullable();     // Hubungan user RT ke RW
            $table->unsignedBigInteger('rt_id')->nullable();     // Optional, jika ingin tahu user ini RT mana
            $table->unsignedBigInteger('daerah_id')->nullable(); // Daerah user

            $table->foreign('rw_id')->references('id')->on('rws')->nullOnDelete();
            $table->foreign('rt_id')->references('id')->on('rts')->nullOnDelete();
            $table->foreign('daerah_id')->references('id')->on('daerahs')->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        // Hapus kolom 'role' hanya jika tabel users sudah ada
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        Schema::dropIfExists('users');
    }
};
