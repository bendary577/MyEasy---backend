<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_store')->default(false);
            $table->integer('customers_number')->default(0);
            $table->float('delivery_speed')->nullable();
            $table->enum('badge', ['bronze', 'silver','gold'])->default('bronze');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
