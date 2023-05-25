<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductToWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_to_website', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->foreignId('product_id');
            $table->foreignId('website_id');

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->foreign('website_id')->references('id')->on('website');
            $table->unique(['product_id', 'website_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_to_website');
    }
}
