<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductTableAddVariantId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->after('country_id');
            $table->foreign('variant_id')->references('id')->on('product_variant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            //\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['variant_id']);
        });
    }
}
