<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class AlterTableProductMakeColumnsNullable extends Migration
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

        Schema::table('product', function (Blueprint $table) {
            $table->string('asin', 10)->nullable()->default(null)->change();
            $table->double('price_msrp', 8, 2)->nullable()->default(null)->change();
            $table->double('price_current', 8, 2)->nullable()->default(null)->change();
            $table->text('notes')->nullable()->default(null);

            // $table->foreign('category_id')->references('id')->on('category');
            // $table->foreign('brand_id')->references('id')->on('brand');
            // $table->foreign('country_id')->references('id')->on('country');

            // $table->foreign('currency_msrp')->references('id')->on('currency');
            // $table->foreign('currency_current')->references('id')->on('currency');
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

        Schema::table('product', function (Blueprint $table) {
            $table->string('asin',10)->nullable(false)->default()->change();
            $table->double('price_msrp', 8, 2)->nullable(false)->default()->change();
            $table->double('price_current', 8, 2)->nullable(false)->default()->change();
            $table->dropColumn('notes');
        });
    }
}
