<?php

namespace Database\Seeders;

use App\Models\Mood;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MoodSeeder extends Seeder
{
    public function run()
    {

        $moods = [['name' => 'Alegre'], ['name' => 'Agresivo'], ['name' => 'Oscuro'], ['name' => 'Triste'], ['name' => 'Sentimental'], ['name' => 'Épico']];

        foreach ($moods as $mood) {
            Mood::create($mood);
        }


    }
}

