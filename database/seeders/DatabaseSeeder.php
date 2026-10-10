<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(SiteSettingSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(AuthorSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(BreakingNewsSeeder::class);
        $this->call(ChartArchiveSeeder::class);
    }
}
