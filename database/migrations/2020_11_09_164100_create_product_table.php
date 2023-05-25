<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('brand_id')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('model');
            $table->string('model_family')->nullable();
            $table->date('date_publish');
            $table->double('price_msrp', 8, 2)->default(0);
            $table->foreignId('currency_msrp')->nullable();
            $table->double('price_current', 8, 2)->default(0);
            $table->foreignId('currency_current')->nullable();
            $table->unsignedSmallInteger('size_length')->nullable();
            $table->unsignedSmallInteger('size_width')->nullable();
            $table->unsignedSmallInteger('size_height')->nullable();
            $table->unsignedSmallInteger('weight')->nullable();
            $table->boolean('is_promote')->default(false);
            $table->string('excerpt')->nullable();
            $table->string('summary_main', 500)->nullable();
            $table->string('summary_value', 500)->nullable();
            $table->text('full_overview')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category');
            $table->foreign('brand_id')->references('id')->on('brand');
            $table->foreign('country_id')->references('id')->on('country');

            $table->foreign('currency_msrp')->references('id')->on('currency');
            $table->foreign('currency_current')->references('id')->on('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
