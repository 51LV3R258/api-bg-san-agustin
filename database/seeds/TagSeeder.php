<?php

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->insert([
            'nombre' => 'agua'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'gaseosa'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'galletas'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'verdura'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'fruta'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'bebida energética'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'cigarros'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'piqueos'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'leche'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'enlatado'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'bebidas alcohólicas'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'bebidas'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'plástico'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'vidrio'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'metal'
        ]);
        DB::table('tags')->insert([
            'nombre' => 'fideos'
        ]);
    }
}
