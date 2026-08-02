<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_admin_can_view_all_resource_index_pages(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $paths = [
            '/admin/books',
            '/admin/users',
            '/admin/categories',
            '/admin/languages',
            '/admin/settings',
            '/admin/activity-logs',
            '/admin/queue-monitor',
            '/admin/system-logs',
        ];

        foreach ($paths as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_non_admin_cannot_access_panel(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_panel_is_localized_to_uzbek(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Tizimga kirish')
            ->assertSee('Parol');

        $this->actingAs($admin)->get('/admin/books')
            ->assertOk()
            ->assertSee('Kitoblar')
            ->assertSee('Sarlavha');

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Asosiy panel')
            ->assertSee('Faoliyat jurnali');
    }

    public function test_admin_can_view_book_create_and_edit_wizard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
        $book = Book::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)->get('/admin/books/create')
            ->assertOk()
            ->assertSee('Sarlavha');

        $this->actingAs($admin)->get("/admin/books/{$book->id}/edit")
            ->assertOk()
            ->assertSee('Muqova yuklash')
            ->assertSee('Bo\'limlar');
    }
}
