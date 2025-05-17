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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('item');
            $table->decimal('price', 8, 2);
            $table->integer('quantity')->default(1);
            $table->string('size')->default('small');
            $table->string('image')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('phone_number')->nullable();  // Make it nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};