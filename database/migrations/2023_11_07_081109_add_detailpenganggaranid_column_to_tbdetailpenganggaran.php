<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailpenganggaranidColumnToTbdetailpenganggaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtransdetailsarpras', function (Blueprint $table) {
            $table->foreignId('detailpenganggaranid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbtransdetailsarpras', function (Blueprint $table) {
            $table->dropColumn('detailpenganggaranid');
        });
    }
}
