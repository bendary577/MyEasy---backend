<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{

    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->float('total_price');
            $table->enum('status', ['pending','received', 'canceled', 'being prepared', 'on the way','delivered'])->default('pending');
            $table->boolean('customer_confirm')->default(false);
            $table->boolean('store_confirm')->default(false);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
