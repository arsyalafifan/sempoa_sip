<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSarprasKebutuhansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbtranssarpraskebutuhan', function (Blueprint $table) {
            $table->id('sarpraskebutuhanid')->autoIncrement();
            $table->foreignId('sekolahid')->nullable();
            $table->string('nopengajuan')->nullable();
            $table->date('tglpengajuan')->nullable();
            $table->string('pegawaiid', )->nullable();
            $table->integer('jenissarpras')->nullable();
            $table->foreignId('namasarprasid')->nullable();
            $table->integer('jumlah')->nullable();
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
        Schema::dropIfExists('tbtranssarpraskebutuhan');
    }
}
