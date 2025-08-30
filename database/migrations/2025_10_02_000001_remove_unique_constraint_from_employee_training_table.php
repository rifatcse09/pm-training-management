<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueConstraintFromEmployeeTrainingTable extends Migration
{
    public function up()
    {
        Schema::table('employee_training', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['training_id']);

            // Drop the unique constraint
            $table->dropUnique('employee_training_employee_id_training_id_unique');

            // Re-add the foreign key constraints without the unique constraint
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('employee_training', function (Blueprint $table) {
            // Drop the foreign key constraints
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['training_id']);

            // Re-add the unique constraint
            $table->unique(['employee_id', 'training_id']);

            // Re-add the foreign key constraints
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('training_id')->references('id')->on('trainings')->cascadeOnDelete();
        });
    }
}
