<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPriceChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_change', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->foreignId('product_id');
            $table->string('price_type');
            $table->double('price_old', 8, 2)->nullable();
            $table->foreignId('currency_old_id');
            $table->double('price_new', 8, 2)->nullable();
            $table->foreignId('currency_new_id');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('product');
            $table->foreign('currency_old_id')->references('id')->on('currency');
            $table->foreign('currency_new_id')->references('id')->on('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_price_change');
    }
}
