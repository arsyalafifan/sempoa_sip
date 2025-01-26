<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPenganggaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbdetailpenganggaran', function (Blueprint $table) {
            $table->id('detailpenganggaranid')->autoIncrement();
            $table->foreignId('sarpraskebutuhanid')->nullable();
            $table->foreignId('statuskebutuhansarprasid')->nullable();
            $table->foreignId('subkegid')->nullable();
            $table->string('sumberdana', 50)->nullable();
            $table->string('subdetailkegiatan')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan', 50)->nullable();
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
        Schema::dropIfExists('tbdetailpenganggaran');
    }
}
