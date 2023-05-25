<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppTableLogoVideoDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app', function (Blueprint $table) {
            $table->string('logo', 255)->nullable()->after('name');
            $table->string('video_url', 255)->nullable()->after('logo');
            $table->string('description', 512)->nullable()->after('video_url');
            $table->string('change_log_url')->nullable($value = true)->change();
            $table->dropColumn(['price']);
            $table->unsignedTinyInteger('price_type')->after('type_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app', function (Blueprint $table) {
            $table->dropColumn(['logo', 'video_url', 'description', 'price_type']);
            $table->double('price', 8, 2)->after('type_id');
        });
    }
}
