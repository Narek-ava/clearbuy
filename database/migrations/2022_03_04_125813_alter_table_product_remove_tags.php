<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductRemoveTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('product', 'tags')) {
          Schema::table('product', function (Blueprint $table) {
              $table->dropColumn('tags');
          });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('product', 'tags')) {
          Schema::table('product', function (Blueprint $table) {
              $table->string('tags')->nullable();
          });
        }
    }
}
