<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deal_prices', function (Blueprint $table) {
\Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            $table->id();
            $table->foreignId('agent_id')->constrained('agent');
            $table->foreignId('product_id')->constrained('product')->onDelete('cascade');
            $table->double('price', 8, 2);
            $table->foreignId('currency_id')->constrained('currency');
            $table->string('url')->nullable()->default(null);
            $table->string('coupon_code')->nullable()->default(null);
            $table->date('expiry_date');
            $table->boolean('is_hot')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deal_prices');
    }
}
