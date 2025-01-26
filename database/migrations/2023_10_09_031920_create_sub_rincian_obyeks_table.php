<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubRincianObyeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmsroby', function (Blueprint $table) {
            $table->id('srobyid')->autoIncrement();
            $table->foreignId('robyid');
            $table->string('srobykode');
            $table->string('srobynama');
            $table->string('tahun', 4);
            $table->boolean('isskpd')->default('0')->nullable();
            $table->boolean('isssh')->default('1')->nullable();
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
        Schema::dropIfExists('tbmsroby');
    }
}
