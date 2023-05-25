<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductPricesNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->float('current_msrp', 8, 2)->nullable($value = true)->change();
            $table->float('original_msrp', 8, 2)->nullable($value = true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->float('current_msrp', 8, 2)->nullable($value = false)->change();
            $table->float('original_msrp', 8, 2)->nullable($value = false)->change();
        });
    } 
}
