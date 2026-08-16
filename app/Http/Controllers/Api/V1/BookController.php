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
    /**
     * Used when the client requests no explicit page/per_page, so it
     * receives the full catalog to filter/sort client-side.
     */
    protected const UNPAGINATED_PER_PAGE = 1000;

    public function __construct(protected BookService $books) {}

    /**
     * List books
     *
     * Paginated, filterable list of books. Supports `q`, `author`,
     * `category_id`, `language_id`, `user_id`, `rating`, `min_rating`,
     * `min_duration`, `max_duration`, `sort` and `per_page` query parameters.
     * When neither `page` nor `per_page` is given, all matching books are
     * returned in a single page.
     */
    public function index(BookIndexRequest $request): JsonResponse
    {
        $perPage = match (true) {
            $request->has('per_page') => (int) $request->input('per_page'),
            $request->has('page') => 15,
            default => self::UNPAGINATED_PER_PAGE,
        };

        $books = $this->books->list($request->validated(), $perPage);

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
