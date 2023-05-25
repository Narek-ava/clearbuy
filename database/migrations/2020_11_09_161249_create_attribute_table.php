<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            /**
             * type:
             * 0 – numeric
             * 1 – text
             * 2 – boolean
             * 3 – date-time
             * 4 – one from list options
             * 5 - many from list options
             */

            /**
             * kind:
             * 1 - Specification
             * 2 - Additional product info
             */

            $table->id()->autoincrement();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('type')->default(1);
            $table->unsignedTinyInteger('kind')->default(1);
            $table->foreignId('measure_id')->nullable();
            $table->foreignId('attribute_group_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('attribute_group_id')->references('id')->on('attribute_group');
            $table->foreign('measure_id')->references('id')->on('measure');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute');
    }
}
