<?php

use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            'nombre' => 'Kg'
        ]);
        DB::table('units')->insert([
            'nombre' => 'unidad'
        ]);
        DB::table('units')->insert([
            'nombre' => 'docena'
        ]);
        DB::table('units')->insert([
            'nombre' => 'caja'
        ]);
    }
}
