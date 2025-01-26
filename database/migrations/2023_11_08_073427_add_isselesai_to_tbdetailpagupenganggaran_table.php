<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsselesaiToTbdetailpagupenganggaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdetailpagupenganggaran', function (Blueprint $table) {
            $table->boolean('isselesai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdetailpagupenganggaran', function (Blueprint $table) {
            $table->dropColumn('isselesai');
        });
    }
}
