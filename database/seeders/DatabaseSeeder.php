<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(RoleAndPermissionSeeder::class);

        $ownerUser = User::firstOrCreate(
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'phone' => '+96311223344',
                'password' => Hash::make('admin'),
                'user_type' => 'owner',
                'department' => 'admin' 
            ]
        );

        if(!$ownerUser->hasRole('owner')){
            $ownerUser->assignRole('owner');
        }
    }
}
