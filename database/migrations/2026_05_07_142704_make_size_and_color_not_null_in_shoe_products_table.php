<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
        public function up()
    {
        Schema::table('shoe_products', function (Blueprint $table) {
            $table->string('size')->nullable(false)->change();
            $table->string('color')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('shoe_products', function (Blueprint $table) {
            $table->string('size')->nullable()->change();
            $table->string('color')->nullable()->change();
        });
    }
};