<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisKebutuhanColumnToTbtranssarpraskebutuhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtranssarpraskebutuhan', function (Blueprint $table) {
            $table->tinyInteger('jeniskebutuhan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbtranssarpraskebutuhan', function (Blueprint $table) {
            $table->dropColumn('jeniskebutuhan');
        });
    }
}
