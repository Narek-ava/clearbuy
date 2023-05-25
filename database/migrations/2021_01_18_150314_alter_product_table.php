<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('product')) {
            if (!Schema::hasColumn('product', 'released_with_os_id')) {
                Schema::table('product', function (Blueprint $table) {
                    $table->foreignId('released_with_os_id')->nullable();
                    $table->foreign('released_with_os_id')->references('id')->on('os');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('product')) {
            if (Schema::hasColumn('product', 'released_with_os_id')) {
                Schema::table('product', function (Blueprint $table) {
                    $table->dropForeign(['released_with_os_id']);
                    $table->dropColumn('released_with_os_id');
                });
            }
        }
    }
}
