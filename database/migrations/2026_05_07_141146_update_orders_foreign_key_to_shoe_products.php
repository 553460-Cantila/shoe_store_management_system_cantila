<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_menu_id_foreign');

            $table->foreign('shoe_product_id')->references('id')->on('shoe_products')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shoe_product_id']);
            $table->foreign('shoe_product_id')->references('id')->on('menus')->onDelete('restrict');
        });
    }
};