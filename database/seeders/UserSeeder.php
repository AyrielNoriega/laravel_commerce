<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # crear usuario de prueba
        \App\Models\User::factory()->create([
            'name' => 'jon',
            'email' => 'jon@gmail.com',
            'password' => bcrypt('123456'),
        ]);
        \App\Models\User::factory(10)->create();
    }
}
