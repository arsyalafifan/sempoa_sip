<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAbsensiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbabsensi', function (Blueprint $table) {
            $table->id('tbabsensiid'); // Primary key (absensi_id)
            $table->foreignId('muridid');
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Tidak Hadir', 'Izin']);
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
     * Run the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::dropIfExists('tbabsensi');
    }
}
