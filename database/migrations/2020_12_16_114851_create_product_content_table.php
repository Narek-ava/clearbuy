<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_content', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->foreignId('product_id');
            $table->integer('type_id')->default(1);
            $table->string('title');
            $table->string('url');
            $table->string('description', 500)->nullable();

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_content');
    }
}
