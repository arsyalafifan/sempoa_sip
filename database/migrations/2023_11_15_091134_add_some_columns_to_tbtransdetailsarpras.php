<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsToTbtransdetailsarpras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtransdetailsarpras', function (Blueprint $table) {
            $table->string('thperolehan', 4)->nullable();
            $table->string('subdetailkegiatan', 300)->nullable();
            $table->string('koderekening', 50)->nullable();
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
            $table->dropColumn('thperolehan');
            $table->dropColumn('subdetailkegiatan');
            $table->dropColumn('koderekening');
        });
    }
}
