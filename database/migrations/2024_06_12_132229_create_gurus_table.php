<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGurusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbguru', function (Blueprint $table) {
            $table->id('guruid')->autoIncrement();
            $table->string('kodeguru')->nullable();
            $table->date('tglmasuk')->nullable();
            $table->string('namaguru')->nullable();
            $table->string('namapanggilan')->nullable();
            $table->smallInteger('jeniskelamin')->nullable();
            $table->string('alamat')->nullable();
            $table->string('tempatlahir')->nullable();
            $table->smallInteger('agama')->nullable();
            $table->date('tgllahir')->nullable();
            $table->string('notelp')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('email')->nullable();
            $table->smallInteger('level')->nullable();
            $table->smallInteger('status')->nullable();
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
        Schema::dropIfExists('tbguru');
    }
}
