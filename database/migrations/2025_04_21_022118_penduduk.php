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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('no_nik')->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('status', ['Kawin', 'Belum Kawin', 'Janda', 'Duda']);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']);
            $table->string('pendidikan');
            $table->string('pekerjaan');
            $table->string('kep_di_kelurahan');
            $table->string('alamat');
            $table->date('tgl_mulai');

            // Relasi
            $table->unsignedBigInteger('user_id'); // yang input datanya
            $table->unsignedBigInteger('rt_id')->nullable();  // relasi ke tabel rts
            $table->unsignedBigInteger('rw_id')->nullable();  // relasi ke tabel rws
            $table->unsignedBigInteger('daerah_id')->nullable(); // relasi ke tabel daerahs

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rt_id')->references('id')->on('rts')->nullOnDelete();
            $table->foreign('rw_id')->references('id')->on('rws')->nullOnDelete();
            $table->foreign('daerah_id')->references('id')->on('daerahs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
