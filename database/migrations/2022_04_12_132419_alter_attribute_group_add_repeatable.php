<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttributeGroupAddRepeatable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attribute_group', function (Blueprint $table) {
            $table->boolean('repeatable')->after('sort_order')->default('0');
            $table->foreignId('parent_id')->nullable()->after('repeatable');
            $table->foreignId('product_id')->nullable()->after('parent_id');

            $table->foreign('parent_id')->references('id')->on('attribute_group')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn(['repeatable', 'parent_id', 'product_id']);
        });
    }
}
