<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductPricesMakeColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->foreignId('agent_id')->nullable()->default(null)->change();
            $table->foreignId('product_id')->nullable()->default(null)->change();
            $table->foreignId('currency_id')->nullable()->default(null)->change();
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
            $table->foreignId('agent_id')->nullable(false)->default()->change();
            $table->foreignId('product_id')->nullable(false)->default()->change();
            $table->foreignId('currency_id')->nullable(false)->default()->change();
        });
    }
}
