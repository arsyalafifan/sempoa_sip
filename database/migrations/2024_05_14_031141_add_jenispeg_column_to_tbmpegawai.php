<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenispegColumnToTbmpegawai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbmpegawai', function (Blueprint $table) {
            $table->smallInteger('jenispeg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbmpegawai', function (Blueprint $table) {
            $table->dropColumn('jenispeg');
        });
    }
}
