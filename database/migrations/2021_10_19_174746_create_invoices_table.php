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
            $table->string('code');
            $table->string('customer_name');
            $table->float('total_price');
            $table->boolean('paid')->default(false);
            $table->enum('currency', ['EGP', 'USD']);
            $table->date('paid_at')->nullable();
            $table->date('expiration_date');
            $table->integer('number_of_items');
            $table->string('invocie_type')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
