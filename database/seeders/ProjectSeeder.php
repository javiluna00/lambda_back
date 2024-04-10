<?php

namespace Database\Seeders;
use App\Models\Project;
use App\Models\Proyecto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProjectSeeder extends Seeder
{
    public function run()
    {

        $proyectos = [
            [
                'name' => 'Serpiente',
                'description' => 'Producción del beat de la canción serpiente de Wide P, JLop y Sane.',
                'image' => 'https://is1-ssl.mzstatic.com/image/thumb/Music116/v4/b0/0e/c7/b00ec770-050f-d37b-d526-2828b9ae457b/artwork.jpg/1200x1200bf-60.jpg',
                'yt_link' => 'https://www.youtube.com/watch?v=-olYAGm35Bc',
                'spotify_link' => '/track/54bZ1BMrHudr44CkBAmMI8?si=d459fcebf40d4854',
            ],
            [
                'name' => '33/19 (Remix)',
                'description' => 'Producción del remix de 33/19, de Wide P.',
                'image' => 'https://is1-ssl.mzstatic.com/image/thumb/Music116/v4/7a/49/18/7a491804-bc22-f8db-d5de-18e200711b41/artwork.jpg/1200x1200bb.jpg',
                'yt_link' => 'https://www.youtube.com/watch?v=FwfgPvu6Q3k',
                'spotify_link' => '/track/4IFFB2gFhcLX4wKM37e3eL?si=cd5f84c7bf914e21',
            ],
            [
                'name' => 'Gunship',
                'description' => 'Producción de dos canciones del EP Gunship de JLop.',
                'image' => 'https://is1-ssl.mzstatic.com/image/thumb/Music126/v4/c8/a1/0c/c8a10c5c-02d8-670b-dac4-85fed2765a5d/artwork.jpg/486x486bb.png',
                'yt_link' => 'https://www.youtube.com/watch?v=4Yw9Y4n5GpA',
                'spotify_link' => '/album/0vCNZq9lszHAaXen7S0owX?si=09a529ac0cbf4fd1',
            ],
            [
                'name' => 'CNI',
                'description' => 'Producción del single CNI de Wide P.',
                'image' => 'https://is1-ssl.mzstatic.com/image/thumb/Music126/v4/96/ea/18/96ea187b-8524-5dff-d2ce-a3b0f0248a96/artwork.jpg/1200x1200bb.jpg',
                'yt_link' => 'https://www.youtube.com/watch?v=NVjWHqfIGO4',
                'spotify_link' => '/album/4gyKkiHcYlarlrLwIo0WFd?si=84a1b0177edb414e',
            ],
            [
                'name' => 'Muerto en vida',
                'description' => 'Producción del single Muerto en Vida de Percless.',
                'image' => 'https://i1.sndcdn.com/artworks-2zB8wDqzCMsU-0-t500x500.png',
                'yt_link' => 'https://www.youtube.com/watch?v=Plmtidd0lD0',
                'spotify_link' => '/track/5cfA3anH7AbVRGBfWiXWMl?si=84c43c65bba14600',
            ],
            [
                'name' => 'Angels (Remix)',
                'description' => 'Producción del remix de Angels, single de Wide P, JLop y Sane.',
                'image' => 'https://is1-ssl.mzstatic.com/image/thumb/Music126/v4/3a/2c/de/3a2cde06-042d-82a9-d511-4f7d5f6ad55b/artwork.jpg/1200x1200bb.jpg',
                'yt_link' => 'https://www.youtube.com/watch?v=kKne-or3P14',
                // 'spotify_link' => no se proporciona, por lo tanto, no se incluye
            ],
        ];

        foreach ($proyectos as $proyecto) {
            Project::create($proyecto);
        }


    }
}

