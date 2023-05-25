<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductAddReviewUrlColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product', 'review_url')) {
            Schema::table('product', function (Blueprint $table) {
                $table->string('review_url')->nullable()->default(null);
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
        if (Schema::hasColumn('product', 'review_url')) {
            Schema::table('product', function (Blueprint $table) {
                $table->dropColumn('review_url');
            });
        }
    }
}
