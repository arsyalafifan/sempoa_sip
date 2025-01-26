<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSatuanColumnToTbtransdetailjumlahsarpras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtransdetailjumlahsarpras', function (Blueprint $table) {
            $table->string('satuan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbtransdetailjumlahsarpras', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
}
