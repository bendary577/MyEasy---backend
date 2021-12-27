<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{

    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_store')->default(false);
            $table->date('birth_date')->nullable();
            $table->float('delivery_speed')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->enum('badge', ['bronze', 'silver','gold'])->default('bronze');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sellers');
    }
}
