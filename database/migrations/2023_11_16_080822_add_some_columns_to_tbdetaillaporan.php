<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsToTbdetaillaporan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdetaillaporan', function (Blueprint $table) {
            $table->tinyInteger('bulan')->nullable();
            $table->decimal('target', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdetaillaporan', function (Blueprint $table) {
            $table->dropColumn('bulan');
            $table->dropColumn('target');
        });
    }
}
