<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTbpegawaipengajuangaji extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbpegawaipengajuangaji', function (Blueprint $table) {
            $table->text('ketpegawai')->nullable();
            $table->text('ketdinas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbpegawaipengajuangaji', function (Blueprint $table) {
            $table->dropColumn('ketpegawai');
            $table->dropColumn('ketdinas');
        });
    }
}
