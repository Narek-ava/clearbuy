<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmRecomendationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('film_recomendation', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->foreignId('film_parent_id');
            $table->foreignId('film_child_id');

            $table->foreign('film_parent_id')->references('id')->on('film')->onDelete('cascade');
            $table->foreign('film_child_id')->references('id')->on('film')->onDelete('cascade');
            $table->unique(['film_parent_id', 'film_child_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('film_recomendation');
    }
}
