<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder {

    public function run()
    {

        $admin = User::create([
            'name' => 'Javi',
            'artist_name' => 'Lambda Beats',
            'email' => 'lambdabeats2017@gmail.com',
            'password' => bcrypt('123123'),
            'email_verified_at' => now()
        ]);

        $admin->assignRole(['admin', 'user']);

    }

}
