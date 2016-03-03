<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesInbox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbox', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fromNumber')->nullable();
            $table->string('to')->nullable();
            $table->text('message')->nullable();
            $table->string('date')->nullable();
            $table->string('linkId')->nullable();
            $table->string('lastReceivedId')->unique()->nullable();
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
        Schema::drop('inbox');
    }
}
