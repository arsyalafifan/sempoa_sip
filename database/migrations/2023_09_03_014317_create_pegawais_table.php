<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmpegawai', function (Blueprint $table) {
            $table->id('pegawaiid')->autoIncrement();
            $table->foreignId('sekolahid')->nullable();
            $table->tinyInteger('unit');
            $table->string('nip');
            $table->tinyInteger('jabatan');
            $table->string('ketjabatan')->nullable();
            $table->string('nama');
            $table->string('npwp');
            $table->string('judulsk')->nullable();
            $table->string('nosk')->nullable();
            $table->date('tgl_sk')->nullable();
            $table->string('file_ttd')->nullable();
            $table->tinyInteger('status')->default('0');
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
        Schema::dropIfExists('tbmpegawai');
    }
}
