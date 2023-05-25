<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsReleaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_release', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->foreignId('os_id')->nullable();
            $table->string('version');
            $table->date('release_date');
            $table->string('added_features', 1000)->nullable();
            $table->timestamps();

            $table->foreign('os_id')->references('id')->on('os')->onDelete('cascade');
        });

        Schema::table('os', function (Blueprint $table) {
            $table->foreign('parent_os_release_id')->references('id')->on('os_release');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('os', function (Blueprint $table) {
            $table->dropForeign(['parent_os_release_id']);
        });

        Schema::dropIfExists('os_release');
    }
}
