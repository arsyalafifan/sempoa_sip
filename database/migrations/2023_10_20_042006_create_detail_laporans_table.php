<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailLaporansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbdetaillaporan', function (Blueprint $table) {
            $table->id('detaillaporanid')->autoIncrement();
            $table->foreignId('detailpaguanggaranid');
            $table->tinyInteger('minggu');
            $table->date('daritgl');
            $table->date('sampaitgl');
            $table->decimal('progres', 5, 2);
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
        Schema::dropIfExists('tbdetaillaporan');
    }
}
