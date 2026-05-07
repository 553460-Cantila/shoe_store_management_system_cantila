<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shoe_products', function (Blueprint $table) {
            $table->string('size')->nullable();   
            $table->string('color')->nullable();  
        });
    }

    public function down(): void
    {
        Schema::table('shoe_products', function (Blueprint $table) {
            $table->dropColumn(['size', 'color']);
        });
    }
};