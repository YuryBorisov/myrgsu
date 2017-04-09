<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVKsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_vk', function (Blueprint $table) {
            $table->integer('id')->unsigned()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->tinyInteger('faculty_id')->unsigned()->default(0);
            $table->integer('group_id')->unsigned()->default(0);
            $table->tinyInteger('call')->default(0);
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
        Schema::dropIfExists('users_vk');
    }
}
