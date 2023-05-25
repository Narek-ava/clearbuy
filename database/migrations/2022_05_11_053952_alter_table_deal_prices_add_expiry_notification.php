<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDealPricesAddExpiryNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deal_prices', function (Blueprint $table) {
            $table->after('expiry_date', function ($table) {
                $table->boolean('expiry_notification')->nullable()->default(null);
            });
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
            $table->dropColumn('expiry_notification');
        });
    }
}
