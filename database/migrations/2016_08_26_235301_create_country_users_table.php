<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountryUsersTable extends Migration
{

    public function up()
    {
        Schema::create('country_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('total_users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop("country_users");
    }
}