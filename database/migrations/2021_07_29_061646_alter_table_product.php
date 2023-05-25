<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product', 'asin')) {
            Schema::table('product', function (Blueprint $table) {
                $table->string('asin', 10)->default(null);
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
        if (Schema::hasColumn('product', 'asin')) {
            Schema::table('product', function (Blueprint $table) {
                $table->dropColumn('asin');
            });
        }
    }
}
