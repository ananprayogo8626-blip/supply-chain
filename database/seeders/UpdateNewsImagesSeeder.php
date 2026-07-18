<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;

class UpdateNewsImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultImage = 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80';
        
        $updated = News::whereNull('image')->update(['image' => $defaultImage]);
        
        $this->command->info("Updated {$updated} news articles with default image.");
    }
}
