<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDealPricesAddRecommendedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('deal_prices', 'recommended')) {
            Schema::table('deal_prices', function (Blueprint $table) {
                $table->boolean('recommended')->default(false);
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
        if (Schema::hasColumn('deal_prices', 'recommended')) {
            Schema::table('deal_prices', function (Blueprint $table) {
                $table->dropColumn('recommended');
            });
        }
    }
}
