<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradeToDesignationsTable extends Migration
{
    public function up()
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->string('grade')->nullable()->after('name'); // Add grade column
        });
    }

    public function down()
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn('grade'); // Remove grade column
        });
    }
}
