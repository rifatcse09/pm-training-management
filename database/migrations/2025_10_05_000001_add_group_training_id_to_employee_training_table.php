<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupTrainingIdToEmployeeTrainingTable extends Migration
{
    public function up()
    {
        Schema::table('group_trainings', function (Blueprint $table) {
            $table->foreignId('group_training_id')
                ->nullable()
                ->after('training_id') // Add the column after 'training_id'
                ->constrained('group_training') // Reference the 'group_training' table
                ->nullOnDelete(); // Set to null if the referenced group is deleted
        });
    }

    public function down()
    {
        Schema::table('employee_training', function (Blueprint $table) {
            $table->dropForeign(['group_training_id']); // Drop the foreign key
            $table->dropColumn('group_training_id'); // Remove the column
        });
    }
}
