<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAppLinksModifyColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $foreignKeys = $this->listTableForeignKeys('app_links');

        if (Schema::hasColumn('app_links', 'agent_id')) {
            Schema::table('app_links', function (Blueprint $table) use ($foreignKeys) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');

                if(in_array('app_links_agent_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['agent_id']);
                }
                $table->dropColumn('agent_id');
            });
        }

        if (Schema::hasColumn('app_links', 'os_id')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->dropForeign(['os_id']);
                $table->dropColumn('os_id');
            });
        }


        Schema::table('app_links', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->foreignId('currency_id')->nullable()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->onUpdate('cascade')->onDelete('set null');

            $table->foreign('currency_id')->references('id')->on('currency')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('app_stores')->onDelete('cascade');

            $table->boolean('free')->default(false);
            $table->boolean('app_purchase')->default(false);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('app_links', 'agent_id')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->foreignId('agent_id')->constrained('agent')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('app_links', 'os_id')) {
            Schema::table('app_links', function (Blueprint $table) {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
                $table->foreignId('os_id')->constrained('os')->onDelete('cascade');
            });
        }

        Schema::table('app_links', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->dropForeign(['currency_id','store_id']);
            $table->dropColumn(['free', 'app_purchase', 'currency_id', 'store_id']);
        });
    }


    //get list of foreign keys
    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function($key) {
    		    return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }
}
