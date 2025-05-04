<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorsTableSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            [
                'name' => 'Merah',
                'value' => 'red',
                'class' => 'bg-red-500'
            ],
            [
                'name' => 'Putih',
                'value' => 'white',
                'class' => 'bg-white'
            ],
            [
                'name' => 'Hitam',
                'value' => 'black',
                'class' => 'bg-black'
            ],
            [
                'name' => 'Biru',
                'value' => 'blue',
                'class' => 'bg-blue-500'
            ],
            [
                'name' => 'Kuning',
                'value' => 'yellow',
                'class' => 'bg-yellow-500'
            ],
            [
                'name' => 'Hijau',
                'value' => 'green',
                'class' => 'bg-green-500'
            ],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
