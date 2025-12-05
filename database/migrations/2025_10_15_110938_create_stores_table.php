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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users');
            $table->enum('type', ['Restaurants', 'Coffee', 'Cinemas', 'Deals']);
            $table->string('name')->nullable();
            $table->text('slug')->nullable();
            $table->string('slogan')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->mediumText('address')->nullable();
            $table->longText('details')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->text('longitude')->nullable();
            $table->text('latitude')->nullable();
            $table->boolean('reservation')->default(false);
            $table->enum('status', ['Pending', 'Active', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
