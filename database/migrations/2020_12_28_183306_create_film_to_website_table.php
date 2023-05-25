<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmToWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('film_to_website', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->foreignId('film_id');
            $table->foreignId('website_id');
            $table->string('link')->nullable();

            $table->foreign('film_id')->references('id')->on('film')->onDelete('cascade');
            $table->foreign('website_id')->references('id')->on('website')->onDelete('cascade');
            $table->unique(['film_id', 'website_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('film_to_website');
    }
}
