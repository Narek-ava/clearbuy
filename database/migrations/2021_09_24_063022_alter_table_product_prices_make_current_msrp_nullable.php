<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class AlterTableProductPricesMakeCurrentMsrpNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        Schema::table('product_prices', function (Blueprint $table) {
            $table->double('current_msrp', 8, 2)->nullable(true)->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        
        Schema::table('product_prices', function (Blueprint $table) {
            $table->double('current_msrp', 8, 2)->nullable(false)->default()->change();
        });
    }
}
