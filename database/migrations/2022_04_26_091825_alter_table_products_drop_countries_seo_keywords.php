<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductsDropCountriesSeoKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('product', 'seo_keywords')) {
            Schema::table('product', function (Blueprint $table) {
                $table->dropColumn('seo_keywords');
            });
        }

        if (Schema::hasColumn('product', 'country_id')) {
            Schema::table('product', function (Blueprint $table) {
                $table->dropForeign('product_country_id_foreign');
                $table->dropColumn('country_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('product', 'seo_keywords')) {
            Schema::table('product', function (Blueprint $table) {
                $table->string('seo_keywords')->nullable();
            });
        }

        if (!Schema::hasColumn('product', 'country_id')) {
            Schema::table('product', function (Blueprint $table) {
                $table->foreignId('country_id')->nullable();
                $table->foreign('country_id')->references('id')->on('country');
            });
        }
    }
}
