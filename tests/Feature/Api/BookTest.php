<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsApiUser(?User $user = null): User
    {
        $user ??= User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}");

        return $user;
    }

    public function test_user_can_create_a_book(): void
    {
        $this->actingAsApiUser();

        $response = $this->postJson('/api/v1/books', [
            'title' => 'The Hobbit',
            'author' => 'J.R.R. Tolkien',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'The Hobbit');

        $this->assertDatabaseHas('books', ['title' => 'The Hobbit']);
    }

    public function test_book_creation_requires_a_title(): void
    {
        $this->actingAsApiUser();

        $this->postJson('/api/v1/books', [])->assertStatus(422);
    }

    public function test_user_can_list_and_filter_books(): void
    {
        $user = $this->actingAsApiUser();
        $category = Category::create(['name' => 'Fiction', 'slug' => 'fiction']);

        Book::factory()->count(2)->create(['user_id' => $user->id, 'category_id' => $category->id]);
        Book::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/books?category_id='.$category->id);

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_owner_can_update_their_book(): void
    {
        $user = $this->actingAsApiUser();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->putJson("/api/v1/books/{$book->id}", ['title' => 'Updated Title'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_non_owner_cannot_update_or_delete_another_users_book(): void
    {
        $owner = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApiUser(User::factory()->create(['role' => User::ROLE_USER]));

        $this->putJson("/api/v1/books/{$book->id}", ['title' => 'Hacked'])->assertForbidden();
        $this->deleteJson("/api/v1/books/{$book->id}")->assertForbidden();
    }

    public function test_admin_can_update_any_book(): void
    {
        $owner = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApiUser(User::factory()->create(['role' => User::ROLE_ADMIN]));

        $this->putJson("/api/v1/books/{$book->id}", ['title' => 'Admin Edit'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Admin Edit');
    }

    public function test_owner_can_delete_their_book(): void
    {
        $user = $this->actingAsApiUser();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->deleteJson("/api/v1/books/{$book->id}")->assertOk();

        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }
}
