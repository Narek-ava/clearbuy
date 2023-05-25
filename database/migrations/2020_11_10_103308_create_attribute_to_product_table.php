<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_to_product', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->foreignId('product_id');
            $table->foreignId('attribute_id');
            $table->foreignId('attribute_option_id')->nullable();
            $table->double('value_numeric', 8, 2)->nullable();
            $table->string('value_text')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->date('value_date')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('attribute');
            $table->foreign('attribute_option_id')->references('id')->on('attribute_option');
            $table->unique(['product_id', 'attribute_id', 'attribute_option_id'], 'pao_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_to_product');
    }
}
