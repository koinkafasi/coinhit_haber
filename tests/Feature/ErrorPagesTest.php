<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_route_renders_custom_404_page(): void
    {
        config(['app.debug' => false]);

        $response = $this->get('/bu-adres-yok');

        $response->assertNotFound();
        $response->assertSee('404');
        $response->assertSee('Aradığınız sayfa bulunamadı');
    }

    public function test_server_error_renders_custom_500_page(): void
    {
        Route::get('/__test/boom-500', fn () => throw new \RuntimeException('boom'));
        config(['app.debug' => false]);

        $response = $this->get('/__test/boom-500');

        $response->assertStatus(500);
        $response->assertSee('500');
        $response->assertSee('Beklenmeyen bir hata oluştu');
    }

    public function test_error_views_render(): void
    {
        $this->assertStringContainsString('404', view('errors.404')->render());
        $this->assertStringContainsString('500', view('errors.500')->render());
    }
}