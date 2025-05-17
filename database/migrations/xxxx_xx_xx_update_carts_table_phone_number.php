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
            // Make phone_number nullable
            if (Schema::hasColumn('carts', 'phone_number')) {
                $table->string('phone_number')->nullable()->change();
            } else {
                $table->string('phone_number')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'phone_number')) {
                $table->string('phone_number')->nullable(false)->change();
            }
        });
    }
};