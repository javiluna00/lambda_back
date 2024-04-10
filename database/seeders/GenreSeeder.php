<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class GenreSeeder extends Seeder
{
    public function run()
    {

        $genres = [['name' => 'Boom bap'], ['name' => 'Trap'], ['name' => 'New jazz'], ['name' => 'House'], ['name' => 'Pluggnb'], ['name' => 'Detroit'], ['name' => 'Reggaeton']];

        foreach ($genres as $genre) {
            Genre::create($genre);
        }


    }
}

