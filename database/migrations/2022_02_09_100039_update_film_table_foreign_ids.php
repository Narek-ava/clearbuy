<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFilmTableForeignIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('film', function (Blueprint $table) {
            $table->time('runtime')->nullable()->after('trailer_link');
            $table->time('bingetime')->nullable()->after('runtime');
            $table->unsignedSmallInteger('seasons')->nullable()->after('bingetime');
            $table->boolean('still_running')->default(false)->after('seasons');
            $table->date('end_of_release')->nullable()->after('still_running');

            $table->dropForeign(['director_id']);
            $table->dropForeign(['writer_id']);
            $table->dropForeign(['producer_id']);
            $table->dropForeign(['production_company_id']);
            $table->foreign('director_id')->references('id')->on('people')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('writer_id')->references('id')->on('people')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('producer_id')->references('id')->on('people')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('production_company_id')->references('id')->on('people')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('film', function (Blueprint $table) {
            $table->dropColumn(['runtime', 'bingetime', 'seasons', 'still_running', 'end_of_release']);

            $table->dropForeign(['director_id']);
            $table->dropForeign(['writer_id']);
            $table->dropForeign(['producer_id']);
            $table->dropForeign(['production_company_id']);
            $table->foreign('director_id')->references('id')->on('agent');
            $table->foreign('writer_id')->references('id')->on('agent');
            $table->foreign('producer_id')->references('id')->on('agent');
            $table->foreign('production_company_id')->references('id')->on('agent');
        });
    }
}
