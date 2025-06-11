<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::create([
            'usuario' => 'julio',
            'password' => Hash::make('Urdanet.2024'),
            'nivel' => 1,
            'nombre' => 'Julio',
            'estado' => 1,
            'status' => 1
        ]);

        Usuario::create([
            'usuario' => 'alazne',
            'password' => Hash::make('Urdanet.2024'),
            'nivel' => 2,
            'nombre' => 'Alazne',
            'estado' => 1,
            'status' => 1
        ]);
    }
}
