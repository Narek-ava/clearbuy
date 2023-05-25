<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if (Schema::hasColumn('product_prices', 'price')) {
            Schema::table('product_prices', function (Blueprint $table) {
                $table->renameColumn('price','current_msrp');
            });
        }

        if (!Schema::hasColumn('product_prices', 'original_msrp')) {
            Schema::table('product_prices', function (Blueprint $table) {
                $table->double('original_msrp', 8, 2)->nullable(true)->default(null);
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
        if (Schema::hasColumn('product_prices', 'current_msrp')) {
            Schema::table('product_prices', function (Blueprint $table) {
                $table->renameColumn('current_msrp','price');
            });
        }

        if (Schema::hasColumn('product_prices', 'launch_msrp')) {
            Schema::table('product_prices', function (Blueprint $table) {
                $table->dropColumn('launch_msrp');
            });
        }
    }
}
