<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UploadRequest;
use App\Http\Resources\BookFileResource;
use App\Models\Book;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    public function __construct(protected UploadService $uploads) {}

    /**
     * Upload a book file
     *
     * Upload a PDF, DOCX, TXT, MP3 or WAV file and attach it to a book.
     */
    public function store(UploadRequest $request): JsonResponse
    {
        $book = Book::query()->findOrFail($request->integer('book_id'));

        $this->authorize('update', $book);

        $bookFile = $this->uploads->store($book, $request->file('file'));

        return (new BookFileResource($bookFile))
            ->response()
            ->setStatusCode(201);
    }
}
