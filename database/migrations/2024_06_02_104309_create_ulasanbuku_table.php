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
        Schema::create('ulasanbuku', function (Blueprint $table) {
            $table->increments('ulasan_id');
            $table->unsignedInteger('buku_id');
            $table->unsignedInteger('user_id');
            $table->text('ulasan')->nullable();
            $table->integer('rating')->nullable();
            $table->foreign('buku_id')->references('buku_id')->on('buku');
            $table->foreign('user_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasanbuku');
    }
};
