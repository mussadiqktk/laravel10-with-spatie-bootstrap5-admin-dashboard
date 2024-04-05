<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = new User();
        $user1->name = 'admin';
        $user1->email = 'admin@gmail.com';
        $user1->password = Hash::make('admin123');
        $user1->email_verified_at = now();
        $user1->save();
        $user1->assignRole('admin');

        $user2 = new User;
        $user2->name = 'dealer';
        $user2->email = 'dealer@gmail.com';
        $user2->password = Hash::make('test1234');
        $user2->email_verified_at = now();
        $user2->save();
        $user2->assignRole('dealer');

        $user3 = new User;
        $user3->name = 'user';
        $user3->email = 'user@gmail.com';
        $user3->password = Hash::make('test1234');
        $user3->email_verified_at = now();
        $user3->save();
        $user3->assignRole('user');
    }
}
