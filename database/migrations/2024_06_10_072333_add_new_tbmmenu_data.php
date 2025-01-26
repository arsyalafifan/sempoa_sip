<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewTbmmenuData extends Migration
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
                'menuid' => 33,
                'parent' => 'Rombel', 
                'menu' => 'Rombel',
                'url' => 'rombel.index',
                'urutan' => 19,
                'ishide' => 0,
                'jenis' => 1,
            ],
            [
                'menuid' => 34,
                'parent' => 'Jurusan', 
                'menu' => 'Jurusan',
                'url' => 'jurusan.index',
                'urutan' => 20,
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
        Schema::table('tbmmenu', function (Blueprint $table) {
            //
        });
    }
}
