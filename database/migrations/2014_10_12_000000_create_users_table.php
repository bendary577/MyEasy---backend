<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('phone_number');
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('bio')->nullable();
            $table->string('availabel_money_amnt')->default('0');

            $table->string('photo_path')->nullable();
            $table->string('avatar')->default('avatar.png');

            $table->unsignedInteger('profile_id')->nullable();
            $table->string('profile_type')->nullable();
            $table->integer('type');

            $table->boolean('is_blocked')->default(0);
            $table->boolean('account_activated')->default(0);
            $table->string('activation_token');

            $table->timestamp('account_verified_at')->nullable();
            $table->softDeletes();
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
