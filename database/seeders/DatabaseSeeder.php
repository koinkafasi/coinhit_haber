<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedAdminUser();
        $this->call(SiteSettingSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(AuthorSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(BreakingNewsSeeder::class);
        $this->call(ChartArchiveSeeder::class);
    }

    private function seedAdminUser(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@coinhit.local'],
            [
                'name' => 'Site Admin',
                'password' => 'password',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
