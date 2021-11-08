<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->integer('customers_number')->default(0);
            $table->integer('orders_number')->default(0);
            $table->float('delivery_speed')->default(0.0);
            $table->boolean('has_store')->default(0);
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->enum('badge', ['gold', 'silver', 'bronze']);
            $table->string('specialization');
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
        Schema::dropIfExists('sellers');
    }
}
