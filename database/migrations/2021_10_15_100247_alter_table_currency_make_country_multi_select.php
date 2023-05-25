<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCurrencyMakeCountryMultiSelect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('currency', 'country_id')) {
            Schema::table('currency', function (Blueprint $table) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
                $table->string('country_ids')->default(null);
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
        if (!Schema::hasColumn('currency', 'country_id')) {
            Schema::table('currency', function (Blueprint $table) {
                $table->foreignId('country_id');
                $table->foreign('country_id')->references('id')->on('country');
            });
        }

        if (Schema::hasColumn('currency', 'country_ids')) {
            Schema::table('currency', function (Blueprint $table) {
                $table->dropColumn(['country_ids']);
            });
        }
    }
}
