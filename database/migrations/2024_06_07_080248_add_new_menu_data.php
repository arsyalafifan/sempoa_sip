<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewMenuData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tbmmenu')->insert([
            [
                'menuid' => 32,
                'parent' => 'Master', 
                'menu' => 'Kelas',
                'url' => 'kelas.index',
                'urutan' => 18,
                'ishide' => 0,
                'jenis' => 1,
            ],
            // ['kolom1' => 'nilai3', 'kolom2' => 'nilai4'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
