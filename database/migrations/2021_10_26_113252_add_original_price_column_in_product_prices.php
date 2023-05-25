<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginalPriceColumnInProductPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deal_prices', function (Blueprint $table) {
            $table->double('original_price', 8, 2)->nullable()->default(null)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deal_prices', function (Blueprint $table) {
            $table->dropColumn(['original_price']);
        });
    }
}
