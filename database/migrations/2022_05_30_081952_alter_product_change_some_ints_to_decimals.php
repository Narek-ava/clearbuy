<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductChangeSomeIntsToDecimals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->decimal('price_msrp', 8, 2)->nullable()->change();
            $table->decimal('price_current', 8, 2)->nullable()->change();
            $table->decimal('size_length', 8, 2)->nullable()->change();
            $table->decimal('size_width', 8, 2)->nullable()->change();
            $table->decimal('size_height', 8, 2)->nullable()->change();
            $table->decimal('weight', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->float('price_msrp')->nullable()->change();
            $table->float('price_current')->nullable()->change();
            $table->smallInteger('size_length')->nullable()->change();
            $table->smallInteger('size_width')->nullable()->change();
            $table->smallInteger('size_height')->nullable()->change();
            $table->smallInteger('weight')->nullable()->change();
        });
    }
}
