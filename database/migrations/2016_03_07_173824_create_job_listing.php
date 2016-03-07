<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobListing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('contact');
            $table->string('contactName');
            $table->string('positions');
            $table->string('category');
            $table->string('location');
            $table->string('jobType');
            $table->string('salary');
            $table->text('description');
            $table->text('duties');
            $table->text('qualifications');
            $table->text('applicationProcedures');
            $table->timestamp('postDate');
            $table->timestamp('deadline');
            $table->softDeletes();
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
        Schema::drop('jobs');
    }
}
