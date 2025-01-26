<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPaguPenganggaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbdetailpagupenganggaran', function (Blueprint $table) {
            $table->id('detailpaguanggaranid')->autoIncrement();
            $table->foreignId('detailpenganggaranid')->nullable();
            $table->tinyInteger('jenispagu')->nullable();
            $table->double('nilaipagu')->nullable();
            $table->string('nokontrak', 50)->nullable();
            $table->double('nilaikontrak')->nullable();
            $table->foreignId('perusahaanid')->nullable();
            $table->date('tgldari')->nullable();
            $table->date('tglsampai')->nullable();
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
        Schema::dropIfExists('tbdetailpagupenganggaran');
    }
}
