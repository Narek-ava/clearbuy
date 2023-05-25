<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_links', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->string('app_store_name');
            $table->double('price', 8, 2)->default(0);
            $table->string('url');
            $table->foreignId('app_id');
            $table->foreignId('os_id');

            $table->foreign('app_id')->references('id')->on('app')->onDelete('cascade');
            $table->foreign('os_id')->references('id')->on('os');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_links');
    }
}
