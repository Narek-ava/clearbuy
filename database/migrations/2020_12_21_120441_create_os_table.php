<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id()->autoincrement();
            $table->foreignId('brand_id')->nullable();
            $table->foreignId('license_type_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('parent_os_release_id')->nullable();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('change_log_url');
            $table->boolean('is_kernel')->default(false);
            $table->string('description', 1000)->nullable();
            $table->timestamps();

            $table->foreign('license_type_id')->references('id')->on('license_type');
            $table->foreign('brand_id')->references('id')->on('brand');
            $table->foreign('parent_id')->references('id')->on('os');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os');
    }
}
