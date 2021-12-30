<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('profile_id')->nullable();
            $table->string('profile_type')->nullable();
            $table->string('name');
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('bio')->nullable();
            $table->string('activation_token');
            $table->float('availabel_money_amnt')->nullable();
            $table->boolean('blocked')->default(false);
            $table->boolean('account_activated')->default(false);
            $table->date('account_activated_at')->nullable();
            $table->string('forgot_password_code')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
