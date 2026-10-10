<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_uses_env_password_and_can_login(): void
    {
        $password = Str::random(24);
        config([
            'admin.email' => 'admin@coinhit.local',
            'admin.password' => $password,
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@coinhit.local')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check($password, $admin->password));

        $this->assertTrue(Auth::attempt([
            'email' => 'admin@coinhit.local',
            'password' => $password,
            'is_active' => true,
        ]));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_new_users_default_to_editor_role(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->isEditor());
        $this->assertFalse($user->isAdmin());
    }

    public function test_application_locale_and_timezone_are_turkish(): void
    {
        $this->assertSame('tr', config('app.locale'));
        $this->assertSame('Europe/Istanbul', config('app.timezone'));
    }
}