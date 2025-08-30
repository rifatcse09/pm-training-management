<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTrainingTable extends Migration
{
    public function up()
    {
        Schema::create('group_training', function (Blueprint $table) {
            $table->id();
            $table->date('start_date'); // Start date of the training group
            $table->date('end_date'); // End date of the training group
            $table->integer('total_days'); // Total days of the training group
            $table->string('file_link')->nullable(); // Column for storing the uploaded file link
            $table->string('file_name')->nullable(); // Column for storing the uploaded file name
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_training');
    }
}
