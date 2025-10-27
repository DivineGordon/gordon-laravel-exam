<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PageTheme;

class PageThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Classic Dark',
                'primary_color' => '#1a1a1a',
                'secondary_color' => '#8b4513',
                'accent_color' => '#ffffff',
                'text_color' => '#ffffff',
                'background_color' => '#000000',
            ],
            [
                'name' => 'Ocean Blue',
                'primary_color' => '#0077be',
                'secondary_color' => '#00a8e8',
                'accent_color' => '#00ffff',
                'text_color' => '#ffffff',
                'background_color' => '#001f3f',
            ],
            [
                'name' => 'Forest Green',
                'primary_color' => '#2d5016',
                'secondary_color' => '#6b8e23',
                'accent_color' => '#9acd32',
                'text_color' => '#ffffff',
                'background_color' => '#1a1a1a',
            ],
            [
                'name' => 'Sunset Orange',
                'primary_color' => '#ff6b35',
                'secondary_color' => '#f7931e',
                'accent_color' => '#ffd700',
                'text_color' => '#2b2b2b',
                'background_color' => '#fff5e6',
            ],
            [
                'name' => 'Royal Purple',
                'primary_color' => '#6a0dad',
                'secondary_color' => '#9370db',
                'accent_color' => '#dda0dd',
                'text_color' => '#ffffff',
                'background_color' => '#1a0033',
            ],
        ];

        foreach ($themes as $theme) {
            PageTheme::create($theme);
        }
    }
}