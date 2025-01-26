<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSatuanToTbtranssarpraskebutuhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtranssarpraskebutuhan', function (Blueprint $table) {
            $table->string('satuan', 50)->nullable()->after('jumlah');
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
            $table->dropColumn('satuan');
        });
    }
}
