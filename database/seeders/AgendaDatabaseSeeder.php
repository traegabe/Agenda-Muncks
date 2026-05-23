<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agenda\Veiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgendaDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Roberto',
            'email' => 'robertomatos@hotmail.com',
            'password' => Hash::make('Poli@2575'),
        ]);

        User::create([
            'name' => 'Euler',
            'email' => 'euleralburquerque@hotmail.com',
            'password' => Hash::make('Poli@2575'),
        ]);

        Veiculo::create(['placa' => 'OBU7B67', 'modelo' => '16 VW/31.320 CNC 6X4', 'marca' => 'Volkswagen']);
        Veiculo::create(['placa' => 'LWK7787', 'modelo' => 'M.BENZ/L 1620-MUNCK BCO', 'marca' => 'Mercedes-Benz']);
    }
}
