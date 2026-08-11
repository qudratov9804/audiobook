<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchRequest;
use App\Http\Resources\BookResource;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(protected BookService $books) {}

    /**
     * Global search
     *
     * Full-text style search across book titles, authors and descriptions,
     * refinable with `author`, `category_id`, `language_id`, `user_id`,
     * `rating`, `min_rating`, `min_duration`, `max_duration` and `sort`.
     */
    public function index(SearchRequest $request): JsonResponse
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
}
