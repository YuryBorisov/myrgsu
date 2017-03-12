<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('week_id')->unsigned();
            $table->tinyInteger('day_id')->unsigned();
            $table->smallInteger('group_id');
            $table->tinyInteger('time_id')->unsigned()->nullable();
            $table->smallInteger('address_id')->unsigned()->nullable();
            $table->smallInteger('teacher_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
