<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookIndexRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
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
     * `language_id`, `status`, `sort` and `per_page` query parameters.
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
     * Create book
     *
     * Create a new book record. Files are attached separately via the upload endpoint.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->books->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => Book::STATUS_PENDING,
        ]);

        return (new BookResource($book->load(['category', 'language', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get book
     */
    public function show(Request $request, Book $book): BookResource
    {
        $this->authorize('view', $book);

        return new BookResource($book->load(['category', 'language', 'user', 'audioFile', 'finalTranscript']));
    }

    /**
     * Update book
     */
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $this->authorize('update', $book);

        $book = $this->books->update($book, $request->validated());

        return new BookResource($book->load(['category', 'language', 'user']));
    }

    /**
     * Delete book
     *
     * Deletes the book along with its uploaded files, audio chunks and transcripts.
     */
    public function destroy(Request $request, Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        $this->books->delete($book);

        return response()->json(['message' => 'Book deleted successfully.']);
    }
}
