<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalisirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmlegalisir', function (Blueprint $table) {
            $table->id('legalisirid')->autoIncrement();
            $table->foreignId('ijazahid');
            $table->string('pegawaiid')->nullable();
            $table->string('file_ijazah');
            $table->string('file_ktp');
            $table->date('tgl_pengajuan');
            $table->tinyInteger('status')->default('0')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('file_legalisir')->nullable();
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
        Schema::dropIfExists('tbmlegalisir');
    }
}
