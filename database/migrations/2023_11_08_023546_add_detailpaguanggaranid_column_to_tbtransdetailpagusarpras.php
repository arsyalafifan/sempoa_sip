<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailpaguanggaranidColumnToTbtransdetailpagusarpras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtransdetailpagusarpras', function (Blueprint $table) {
            $table->foreignId('detailpaguanggaranid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbtransdetailpagusarpras', function (Blueprint $table) {
            $table->dropColumn('detailpaguanggaranid');
        });
    }
}
