<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttributeGroupChangeNameUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attribute_group', function (Blueprint $table) {
            $table->dropUnique('attribute_group_name_unique');
            $table->unique(['name', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute_group', function (Blueprint $table) {
            $table->dropUnique('attribute_group_name_product_id_unique');
            $table->unique('name');
        });
    }
}
