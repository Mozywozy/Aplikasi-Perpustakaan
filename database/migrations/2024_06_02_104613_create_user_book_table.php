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
        Schema::create('user_book', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('buku_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status', 50)->default('pending');
            $table->timestamps();

            $table->foreign('buku_id')->references('buku_id')->on('buku');
            $table->foreign('user_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_book');
    }
};
