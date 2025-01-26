<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOnTbmijazah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbmijazah', function (Blueprint $table) {
            $table->foreignId('provinsiid')->nullable();
            $table->string('namaprov', 300)->nullable();
            $table->string('namakab', 300)->nullable();
            $table->string('namakec', 300)->nullable();
            $table->string('namasekolah', 300)->nullable();
            $table->string('file_ijazah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbmijazah', function (Blueprint $table) {
            //
        });
    }
}
