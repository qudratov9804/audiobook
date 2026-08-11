<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Books\Pages\EditBook;
use App\Filament\Resources\Books\RelationManagers\SectionsRelationManager;
use App\Models\Book;
use App\Models\BookSection;
use App\Models\User;
use Filament\Actions\CreateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BookResourceFileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_a_cover_from_the_edit_form(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);
        $book = Book::factory()->create(['user_id' => $admin->id]);

        $cover = UploadedFile::fake()->image('cover.jpg', 600, 900);

        Livewire::actingAs($admin)
            ->test(EditBook::class, ['record' => $book->getRouteKey()])
            ->fillForm(['cover_path' => [$cover]])
            ->call('save')
            ->assertHasNoFormErrors();

        $book->refresh();
        $this->assertNotNull($book->cover_path);
        Storage::disk('public')->assertExists($book->cover_path);
    }

    public function test_admin_can_add_a_book_section_with_audio(): void
    {
        Storage::fake('books');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);
        $book = Book::factory()->create(['user_id' => $admin->id]);

        $audio = UploadedFile::fake()->create('chapter1.mp3', 1024, 'audio/mpeg');

        Livewire::actingAs($admin)
            ->test(SectionsRelationManager::class, [
                'ownerRecord' => $book,
                'pageClass' => EditBook::class,
            ])
            ->callTableAction(CreateAction::class, data: [
                'name' => '1-bo\'lim',
                'description' => 'Kirish qismi',
                'path' => [$audio],
            ])
            ->assertHasNoTableActionErrors();

        $section = BookSection::query()->where('book_id', $book->id)->first();
        $this->assertNotNull($section, 'A BookSection record should be created for the uploaded audio.');
        $this->assertSame('1-bo\'lim', $section->name);
        $this->assertSame('mp3', $section->format);
        Storage::disk('books')->assertExists($section->path);
    }
}
