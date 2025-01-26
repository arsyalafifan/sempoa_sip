<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPaguSarprasTersediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbtransdetailpagusarpras', function (Blueprint $table) {
            $table->id('detailpagusarprasid')->autoIncrement();
            $table->foreignId('detailsarprasid')->nullable();
            $table->tinyInteger('jenispagu')->nullable();
            $table->integer('nilaipagu')->nullable();
            $table->foreignId('perusahaanid')->nullable();
            $table->date('tglpelaksanaan')->nullable();
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
        Schema::dropIfExists('detail_pagu_sarpras_tersedias');
    }
}
