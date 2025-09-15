<?php

namespace Database\Seeders;

use App\Http\Controllers\IngredientController;
use App\Models\Ingredient\Ingredient;
use App\Models\Unit\Unit;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'admin',
            'email' => 'test@example.com',
            'password' => Hash::make('12341234')
        ]);

        Unit::create([
            'name' => 'Kg',
            'slug' => 'kg'
        ]);
        Unit::create([
            'name' => 'Gr',
            'slug' => 'gr'
        ]);
        Unit::create([
            'name' => 'Pcs',
            'slug' => 'pcs'
        ]);

        $ingController = new IngredientController();
        
        $ingController->store(new Request([
            'name' => 'ayam',
            'price' => 35000,
            'unit_id' => 1,
            'weight' => 1000,
        ]));

        $ingController->store(new Request([
            'name' => 'bawang putih',
            'price' => 30000,
            'unit_id' => 1,
            'weight' => 1000,
        ]));
        $ingController->store(new Request([
            'name' => 'gula merah',
            'price' => 25000,
            'unit_id' => 3,
            'weight' => 1000,
            'pieces' => 4
        ]));

    } 
}
