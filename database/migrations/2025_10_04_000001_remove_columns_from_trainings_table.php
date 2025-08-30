<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromTrainingsTable extends Migration
{
    public function up()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('start_date'); // Remove start_date column
            $table->dropColumn('end_date'); // Remove end_date column
            $table->dropColumn('total_days'); // Remove total_days column
            $table->dropColumn('file_link'); // Remove file_link column
            $table->dropColumn('file_name'); // Remove file_name column
        });
    }

    public function down()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->date('start_date')->nullable(); // Re-add start_date column
            $table->date('end_date')->nullable(); // Re-add end_date column
            $table->integer('total_days')->nullable(); // Re-add total_days column
            $table->string('file_link')->nullable(); // Re-add file_link column
            $table->string('file_name')->nullable(); // Re-add file_name column
        });
    }
}
