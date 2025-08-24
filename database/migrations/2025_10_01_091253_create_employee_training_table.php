<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTrainingTable extends Migration
{
    public function up()
    {
        Schema::create('employee_training', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete(); // Add designation_id
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('working_place', [1, 2, 3, 4, 5, 6, 7]); // Enum for Working Place 1-7

            $table->unique(['employee_id','training_id']); // one active assignment per training
            $table->index(['training_id','employee_id']);
            $table->softDeletes(); // Add soft delete column
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_training');
    }
}
