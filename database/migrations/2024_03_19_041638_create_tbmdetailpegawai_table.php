<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbmdetailpegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmdetailpegawai', function (Blueprint $table) {
            $table->id('detailpegawaiid')->autoIncrement();
            $table->foreignId('pegawaiid');
            $table->year('tahun')->nullable();
            $table->smallInteger('golpegawaiid')->nullable();
            $table->smallInteger('jenisjab')->nullable();
            $table->smallInteger('eselon')->nullable();
            $table->smallInteger('jabatanid')->nullable();
            $table->smallInteger('golruangberkalaid')->nullable();
            $table->year('msgajiberkalathn')->nullable();
            $table->smallInteger('msgajiberkalabln')->nullable();
            $table->date('tmtberkala')->nullable();
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
        Schema::dropIfExists('tbmdetailpegawai');
    }
}
