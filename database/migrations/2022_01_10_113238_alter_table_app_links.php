<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAppLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('app_links', 'app_store_name')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->dropColumn('app_store_name');
            });
        }

        if (!Schema::hasColumn('app_links', 'agent_id')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->foreignId('agent_id')->constrained('agent')->onDelete('cascade');
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
        if (!Schema::hasColumn('app_links', 'app_store_name')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->string('app_store_name')->default(null);
            });
        }

        if (Schema::hasColumn('app_links', 'agent_id')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->dropForeign('agent_id');
            });
        }
    }
}
