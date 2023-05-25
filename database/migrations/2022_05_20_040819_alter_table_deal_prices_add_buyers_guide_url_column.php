<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDealPricesAddBuyersGuideUrlColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('product', 'buyers_guide_url')) {
            Schema::table('product', function ($table) {
                $table->after('review_url', function ($table) {
                    $table->string('buyers_guide_url')->nullable()->default(null);
                });
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
        if(Schema::hasColumn('product', 'buyers_guide_url')) {
            Schema::table('product', function ($table) {
                $table->dropColumn('buyers_guide_url');
            });
        };
    }
}
