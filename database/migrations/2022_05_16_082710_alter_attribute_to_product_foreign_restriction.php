<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttributeToProductForeignRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attribute_to_product', function (Blueprint $table) {
            $table->dropForeign(['attribute_id']);
            $table->foreign('attribute_id')->references('id')->on('attribute')->onDelete('cascade')->onUpdate('cascade')->change();

            $table->dropForeign(['attribute_option_id']);
            $table->foreign('attribute_option_id')->references('id')->on('attribute_option')->onDelete('cascade')->onUpdate('cascade')->change();

            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute_to_product', function (Blueprint $table) {
            $table->dropForeign(['attribute_id']);
            $table->foreign('attribute_id')->references('id')->on('attribute')->onDelete('restrict')->onUpdate('restrict')->change();

            $table->dropForeign(['attribute_option_id']);
            $table->foreign('attribute_option_id')->references('id')->on('attribute_option')->onDelete('restrict')->onUpdate('restrict')->change();

            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('product')->onDelete('restrict')->onUpdate('restrict')->change();
        });
    }
}
