<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->hasAttached(User::factory()->count(1), ['type' => 0], 'owners')
            ->hasAttached(User::factory()->count(1), ['type' => 0], 'children')
            ->hasAttached(User::factory()->count(1), ['type' => 1], 'children')
            ->create();
    }
}
