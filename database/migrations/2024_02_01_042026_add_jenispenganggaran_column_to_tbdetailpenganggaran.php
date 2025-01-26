<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenispenganggaranColumnToTbdetailpenganggaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdetailpenganggaran', function (Blueprint $table) {
            $table->tinyInteger('jenispenganggaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdetailpenganggaran', function (Blueprint $table) {
            $table->dropColumn('jenispenganggaran');
        });
    }
}
