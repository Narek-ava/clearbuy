<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDealPricesAddRetailerCustomTextColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('deal_prices', 'retailer_custom_text')) {
            Schema::table('deal_prices', function ($table) {
                $table->string('retailer_custom_text')->nullable()->default(null);
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
        if(Schema::hasColumn('deal_prices', 'retailer_custom_text')) {
            Schema::table('deal_prices', function ($table) {
                $table->dropColumn('retailer_custom_text');
            });
        };
    }
}
