<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObyeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbmoby', function (Blueprint $table) {
            $table->id('obyid')->autoIncrement();
            $table->foreignId('jenid');
            $table->string('obykode');
            $table->string('obynama');
            $table->string('tahun', 4);
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
        Schema::dropIfExists('tbmoby');
    }
}
