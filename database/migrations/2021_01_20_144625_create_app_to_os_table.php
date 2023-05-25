<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppToOsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_to_os', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->foreignId('app_id');
            $table->foreignId('os_id');

            $table->foreign('app_id')->references('id')->on('app')->onDelete('cascade');
            $table->foreign('os_id')->references('id')->on('os')->onDelete('cascade');
            $table->unique(['app_id', 'os_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_to_os');
    }
}
