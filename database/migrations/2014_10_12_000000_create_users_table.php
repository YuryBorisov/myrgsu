<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->tinyInteger('service_id')->unsigned();
            $table->bigInteger('user_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->tinyInteger('faculty_id')->unsigned()->default(0);
            $table->integer('group_id')->unsigned()->default(0);
            $table->tinyInteger('location_id')->unsigned()->default(0);
            $table->boolean('call')->default(0);
            $table->boolean('distribution')->default(0);
            $table->dateTime('created_at');
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
