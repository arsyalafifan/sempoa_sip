<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIjazahtemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmijazahtemp', function (Blueprint $table) {
            $table->id('ijazahid')->autoIncrement();
            $table->foreignId('sekolahid')->nullable();
            $table->foreignId('provinsiid')->nullable();
            $table->string('namaprov', 300)->nullable();
            $table->string('namakab', 300)->nullable();
            $table->string('namakec', 300)->nullable();
            $table->string('namasekolah', 300)->nullable();
            $table->string('namasiswa', 300)->nullable();
            $table->string('tempat_lahir', 300)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('namaortu', 300)->nullable();
            $table->string('nis', 20)->nullable();
            $table->string('noijazah', 50)->nullable();
            $table->date('tgl_lulus')->nullable();
            $table->string('file_ijazah')->nullable();
            $table->string('file_ktp')->nullable();
            $table->date('tgl_pengajuan');
            $table->tinyInteger('status')->default('0')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('tbmijazahtemp');
    }
}
