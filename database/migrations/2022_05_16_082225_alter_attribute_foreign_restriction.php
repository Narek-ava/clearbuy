<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttributeForeignRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attribute', function (Blueprint $table) {
            $table->dropForeign(['attribute_group_id']);
            $table->foreign('attribute_group_id')->references('id')->on('attribute_group')->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute', function (Blueprint $table) {
            $table->dropForeign(['attribute_group_id']);
            $table->foreign('attribute_group_id')->references('id')->on('attribute_group')->onDelete('restrict')->onUpdate('restrict')->change();
        });
    }
}
