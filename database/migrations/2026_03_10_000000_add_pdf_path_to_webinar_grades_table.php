<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfPathToWebinarGradesTable extends Migration
{
    public function up()
    {
        Schema::table('webinar_grades', function (Blueprint $table) {
            $table->string('pdf_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('webinar_grades', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
}
