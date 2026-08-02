<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(protected Book $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['category', 'language', 'user']);

        if ($search = $filters['q'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $filters['category_id'] ?? null) {
            $query->where('category_id', $categoryId);
        }

        if ($languageId = $filters['language_id'] ?? null) {
            $query->where('language_id', $languageId);
        }

        if ($userId = $filters['user_id'] ?? null) {
            $query->where('user_id', $userId);
        }

        $sort = $filters['sort'] ?? '-created_at';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        if (in_array($column, ['title', 'created_at', 'duration'], true)) {
            $query->orderBy($column, $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?Book
    {
        return $this->model->with(['category', 'language', 'user', 'sections'])->find($id);
    }

    public function findBySlug(string $slug): ?Book
    {
        return $this->model->with(['category', 'language', 'user', 'sections'])
            ->where('slug', $slug)->first();
    }

    public function create(array $data): Book
    {
        return $this->model->create($data);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->refresh();
    }

    public function delete(Book $book): bool
    {
        return (bool) $book->delete();
    }
}
