<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrgencyColumnToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   

        Schema::table('product', function (Blueprint $table) {
            $table->timestamp('urgency')->nullable()->default(null);
            $table->string('sku')->nullable()->default(null)->change();
            $table->string('model')->nullable()->default(null)->change();
            $table->date('date_publish')->nullable()->default(null)->change();
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
            $table->string('sku')->nullable(false)->change();
            $table->string('model')->nullable(false)->change();
            $table->date('date_publish')->nullable(false)->change();
            $table->dropColumn('urgency');
        });
    }
}
