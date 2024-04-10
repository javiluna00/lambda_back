<?php

namespace Database\Seeders;

use App\Models\License;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenseSeeder extends Seeder
{
    public function run()
    {
       $licenses = [
           [
               'name' => 'Licencia mp3',
               'description' => 'Licencia mp3, la más básica',
               'extension' => '.mp3',
               'conditions' => 'Licencia mp3, la más basica',
               'legal_file_url' => 'https://www.rollingstone.com/wp-content/uploads/2022/11/Beat-1-1.jpg',
           ],
           [
            'name' => 'Licencia wav',
            'description' => 'Licencia wav, la más básica',
            'conditions' => 'Licencia wav, la más basica',
            'extension' => '.wav',
            'legal_file_url' => 'https://www.rollingstone.com/wp-content/uploads/2022/11/Beat-1-1.jpg',
            ],
            [
                'name' => 'Licencia stems',
                'description' => 'Licencia stems, la más básica',
                'conditions' => 'Licencia stems, la más basica',
                'extension' => '.zip',
                'legal_file_url' => 'https://www.rollingstone.com/wp-content/uploads/2022/11/Beat-1-1.jpg',
            ],
            [
                'name' => 'Licencia exclusiva',
                'description' => 'Licencia exclusiva',
                'conditions' => 'Licencia exclusiva',
                'extension' => '.zip',
                'legal_file_url' => 'https://www.rollingstone.com/wp-content/uploads/2022/11/Beat-1-1.jpg',
            ]
        ];

        foreach ($licenses as $license) {
            License::create($license);
        }
    }
}

