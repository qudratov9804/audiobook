<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookIndexRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(protected BookService $books) {}

    /**
     * List books
     *
     * Paginated, filterable list of books. Supports `q`, `category_id`,
     * `language_id`, `sort` and `per_page` query parameters.
     */
    public function index(BookIndexRequest $request): JsonResponse
    {
        $books = $this->books->list(
            $request->validated(),
            (int) $request->input('per_page', 15),
        );

        return response()->json([
            'data' => BookResource::collection($books->items()),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    /**
     * Get book
     */
    public function show(Request $request, Book $book): BookResource
    {
        $this->authorize('view', $book);

        return new BookResource($book->load(['category', 'language', 'user', 'sections']));
    }
}
