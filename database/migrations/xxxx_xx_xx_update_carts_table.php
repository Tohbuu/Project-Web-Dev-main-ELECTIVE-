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
        Schema::table('carts', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('carts', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('carts', 'item')) {
                $table->string('item');
            }
            
            if (!Schema::hasColumn('carts', 'price')) {
                $table->decimal('price', 8, 2);
            }
            
            if (!Schema::hasColumn('carts', 'quantity')) {
                $table->integer('quantity')->default(1);
            }
            
            if (!Schema::hasColumn('carts', 'size')) {
                $table->string('size')->default('small');
            }
            
            if (!Schema::hasColumn('carts', 'image')) {
                $table->string('image')->nullable();
            }
            
            if (!Schema::hasColumn('carts', 'special_instructions')) {
                $table->text('special_instructions')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop these columns in the down method
        // as they might contain important data
    }
};