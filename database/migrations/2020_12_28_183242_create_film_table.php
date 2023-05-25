<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('film', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->string('name');
            $table->date('release_date');
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->foreignId('director_id')->nullable();
            $table->foreignId('writer_id')->nullable();
            $table->foreignId('producer_id')->nullable();
            $table->foreignId('production_company_id')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('age_rating_id')->nullable();
            $table->string('image')->nullable();
            $table->string('trailer_link')->nullable();
            $table->timestamps();

            $table->foreign('director_id')->references('id')->on('agent');
            $table->foreign('writer_id')->references('id')->on('agent');
            $table->foreign('producer_id')->references('id')->on('agent');
            $table->foreign('production_company_id')->references('id')->on('agent');
            $table->foreign('age_rating_id')->references('id')->on('age_rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('film');
    }
}
