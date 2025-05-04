<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizesTableSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['name' => 'S', 'value' => 'S'],
            ['name' => 'M', 'value' => 'M'],
            ['name' => 'L', 'value' => 'L'],
            ['name' => 'XL', 'value' => 'XL'],
            ['name' => '1L', 'value' => '1L'],
            ['name' => '2L', 'value' => '2L'],
            ['name' => '3L', 'value' => '3L'],
            ['name' => '4L', 'value' => '4L'],
            ['name' => '5L', 'value' => '5L'],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
