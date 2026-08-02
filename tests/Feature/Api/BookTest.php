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

    public function test_user_can_view_a_book(): void
    {
        $user = $this->actingAsApiUser();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->getJson("/api/v1/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $book->id);
    }

    public function test_books_endpoint_does_not_allow_writes(): void
    {
        $user = $this->actingAsApiUser();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->postJson('/api/v1/books', ['title' => 'The Hobbit'])->assertStatus(405);
        $this->putJson("/api/v1/books/{$book->id}", ['title' => 'Updated Title'])->assertStatus(405);
        $this->deleteJson("/api/v1/books/{$book->id}")->assertStatus(405);
    }
}
