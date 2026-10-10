<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * İlk admin kullanıcısını oluşturur.
     *
     * Şifre repoya asla yazılmaz: ADMIN_PASSWORD ortam değişkeninden (veya
     * deploy sırasında komut argümanı/secret olarak) okunur. Tanımlı değilse
     * rastgele bir geçici şifre üretilip konsola yazılır.
     */
    public function run(): void
    {
        $email = (string) config('admin.email', 'admin@coinhit.local');
        $name = (string) config('admin.name', 'Site Admin');
        $password = (string) (config('admin.password') ?? '');

        if ($password === '') {
            $password = Str::password(20);
            $this->command?->warn("ADMIN_PASSWORD tanımlı değil. Geçici admin şifresi: {$password}");
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Admin kullanıcı hazır: {$email}");
    }
}