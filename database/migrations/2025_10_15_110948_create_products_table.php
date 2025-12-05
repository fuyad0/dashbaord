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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stores_id')->constrained('stores')->onDelete('no action')->onUpdate('no action');
            $table->text('tags')->nullable();
            $table->string('name')->nullable();
            $table->text('photo')->nullable();
            $table->string('price')->nullable();
            $table->string('offer_type')->nullable();
            $table->longText('offer_des')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
