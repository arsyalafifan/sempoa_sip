<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJumlahGurusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmsekolahjumlahguru', function (Blueprint $table) {
            $table->id('jumlahguruid')->autoIncrement();
            $table->foreignId('sekolahid')->nullable();
            $table->foreignId('tahunajaranid')->nullable();
            $table->integer('statuspegawai')->nullable();
            $table->integer('jumlahguru')->nullable();
            $table->integer('jeniskelamin')->nullable();
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
        Schema::dropIfExists('tbmsekolahjumlahguru');
    }
}
