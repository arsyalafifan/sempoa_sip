<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerusahaanidToTbmuser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbmuser', function (Blueprint $table) {
            $table->foreignId('perusahaanid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbmuser', function (Blueprint $table) {
            $table->dropColumn('perusahaanid');
        });
    }
}
