<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('designation_id');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->enum('working_place', [1, 2, 3, 4, 5, 6, 7]); // Enum for Working Place 1-7
            $table->softDeletes();
            $table->timestamps();

            // Foreign key reference to the designations table
            $table->foreign('designation_id')->references('id')->on('designations');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};