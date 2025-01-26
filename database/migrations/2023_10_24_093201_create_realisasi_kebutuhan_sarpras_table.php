<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealisasiKebutuhanSarprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbrealisasikebutuhansarpras', function (Blueprint $table) {
            $table->id('realisasiid')->autoIncrement();
            $table->foreignId('sarpraskebutuhanid');
            $table->string('nosp2d')->nullable();
            $table->date('tglsp2d')->nullable();
            $table->double('nilai')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('file')->nullable();
            $table->string('opadd', 50)->nullable();
            $table->string('pcadd', 20)->nullable();
            $table->timestamp('tgladd');
            $table->string('opedit', 50)->nullable();
            $table->string('pcedit', 20)->nullable();
            $table->timestamp('tgledit');
            $table->boolean('dlt')->default('0')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbrealisasikebutuhansarpras');
    }
}
