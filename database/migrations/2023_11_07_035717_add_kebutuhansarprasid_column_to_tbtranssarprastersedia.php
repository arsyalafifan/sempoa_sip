<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKebutuhansarprasidColumnToTbtranssarprastersedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtranssarprastersedia', function (Blueprint $table) {
            $table->foreignId('sarpraskebutuhanid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbtranssarprastersedia', function (Blueprint $table) {
            $table->dropColumn('sarpraskebutuhanid');
        });
    }
}
