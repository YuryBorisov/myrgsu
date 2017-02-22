<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTelegramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_telegrams', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->unique();
            $table->smallInteger('faculty_id')->unsigned()->default(0);
            $table->smallInteger('group_id')->unsigned()->default(0);
            $table->boolean('call')->default(false);
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
        Schema::dropIfExists('user_telegrams');
    }
}
