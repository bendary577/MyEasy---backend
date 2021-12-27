<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        
        DB::table('categories')->insert([
            ['name' => 'Home exercise equipment'],
            ['name' => 'Electronics'],
            ['name' => 'Meal boxes and kitchen accessories'],
            ['name' => 'Gaming'],
            ['name' => 'Creative home entertainment'],
            ['name' => 'Furniture'],
            ['name' => 'Groceries'],
            ['name' => 'Arts, Crafts'],
            ['name' => 'Pet supplies'],
            ['name' => 'Books'],
            ['name' => 'Clothing'],
            ['name' => 'Beauty & Personal Care'],
            ['name' => 'Garden & Outdoor'],
            ['name' => 'Exercise/Fitness'],
            ['name' => 'Cars'],
            ['name' => 'Sporting goods'],
            ['name' => 'Watches'],
            ['name' => 'Other'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
