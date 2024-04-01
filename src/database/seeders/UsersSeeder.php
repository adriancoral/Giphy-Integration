<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', '=', 'test@sample.net')->first()) {
            User::factory()->state([
                'name' => 'test',
                'email' => 'test@sample.net',
                'password' => 'test',
            ])->create();
        }
    }
}
