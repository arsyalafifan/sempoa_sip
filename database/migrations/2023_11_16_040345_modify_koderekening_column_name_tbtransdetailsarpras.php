<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyKoderekeningColumnNameTbtransdetailsarpras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbtransdetailsarpras', function (Blueprint $table) {
            DB::statement('ALTER TABLE tbtransdetailsarpras RENAME COLUMN koderekening TO jenid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbtransdetailsarpras', function (Blueprint $table) {
            DB::statement('ALTER TABLE tbtransdetailsarpras RENAME COLUMN jenid TO koderekening');
        });
    }
}
