<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('code');
            $table->string('customer_name');
            $table->float('total_price');
            $table->boolean('paid');
            $table->enum('owner_type', ['seller', 'company']);
            $table->enum('currency', ['EGP', 'USD']);
            $table->date('paid_at');
            $table->date('expiration_date');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
