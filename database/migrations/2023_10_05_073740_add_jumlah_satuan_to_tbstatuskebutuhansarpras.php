<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahSatuanToTbstatuskebutuhansarpras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbstatuskebutuhansarpras', function (Blueprint $table) {
            $table->integer('jumlahsetuju')->nullable();
            $table->string('satuan', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbstatuskebutuhansarpras', function (Blueprint $table) {
            $table->dropColumn('jumlahsetuju');
            $table->dropColumn('satuan');
        });
    }
}
