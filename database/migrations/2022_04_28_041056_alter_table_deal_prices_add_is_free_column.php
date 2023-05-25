<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDealPricesAddIsFreeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('deal_prices', 'is_free')) {
            Schema::table('deal_prices', function ($table) {
                $table->boolean('is_free')->default(false);
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
        if(Schema::hasColumn('deal_prices', 'is_free')) {
            Schema::table('deal_prices', function ($table) {
                $table->dropColumn('is_free');
            });
        };
    }
}
