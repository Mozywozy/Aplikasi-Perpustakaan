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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('peminjaman_id');
            $table->unsignedBigInteger('buku_id');
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_pengembalian')->nullable();
            $table->enum('kondisi_buku', ['Rusak', 'Normal', 'Hilang', 'Telat']);
            $table->string('status', 50)->default('pending');
            $table->integer('denda')->default(0);

            $table->foreign('buku_id')->references('buku_id')->on('buku');
            $table->foreign('user_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
