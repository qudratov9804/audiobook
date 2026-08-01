<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $books,
        protected ActivityLogService $activityLog,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->books->paginate($filters, $perPage);
    }

    public function find(int $id): ?Book
    {
        return $this->books->find($id);
    }

    public function create(array $data): Book
    {
        $book = $this->books->create($data);

        $this->activityLog->log('book.created', $book, "Book \"{$book->title}\" created");

        return $book;
    }

    public function update(Book $book, array $data): Book
    {
        $book = $this->books->update($book, $data);

        $this->activityLog->log('book.updated', $book, "Book \"{$book->title}\" updated");

        return $book;
    }

    public function delete(Book $book): bool
    {
        foreach ($book->files as $file) {
            Storage::disk($file->disk)->delete($file->path);
        }

        foreach ($book->audioFiles as $audioFile) {
            foreach ($audioFile->chunks as $chunk) {
                Storage::disk($chunk->disk)->delete($chunk->path);
            }
            Storage::disk($audioFile->disk)->delete($audioFile->path);
        }

        foreach ($book->transcripts as $transcript) {
            if ($transcript->path) {
                Storage::disk($transcript->disk)->delete($transcript->path);
            }
        }

        $title = $book->title;
        $deleted = $this->books->delete($book);

        $this->activityLog->log('book.deleted', null, "Book \"{$title}\" deleted");

        return $deleted;
    }
}
