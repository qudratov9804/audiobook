<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class StreamController extends Controller
{
    /**
     * Stream audio
     *
     * Streams the book's audio file with HTTP range support, suitable for
     * `<audio>` players and seeking.
     */
    public function stream(Request $request, Book $book): BinaryFileResponse
    {
        $this->authorize('view', $book);

        $audioFile = $book->sections()->latest()->firstOrFail();

        $path = Storage::disk($audioFile->disk)->path($audioFile->path);

        abort_unless(is_file($path), 404, 'Audio file not found on disk.');

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'audio/'.($audioFile->format ?: 'mpeg'));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }

    /**
     * Stream a section's audio
     *
     * Streams a specific book section's audio file, for players that let
     * the listener jump between sections.
     */
    public function streamSection(Request $request, Book $book, BookSection $section): BinaryFileResponse
    {
        $this->authorize('view', $book);

        abort_unless($section->book_id === $book->id, 404);

        $path = Storage::disk($section->disk)->path($section->path);

        abort_unless(is_file($path), 404, 'Audio file not found on disk.');

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'audio/'.($section->format ?: 'mpeg'));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }

    /**
     * Download audio
     *
     * Downloads the book's audio file as an attachment.
     */
    public function download(Request $request, Book $book): BinaryFileResponse
    {
        $this->authorize('view', $book);

        $audioFile = $book->sections()->latest()->firstOrFail();

        $path = Storage::disk($audioFile->disk)->path($audioFile->path);

        abort_unless(is_file($path), 404, 'Audio file not found on disk.');

        $filename = str($book->title)->slug()->append('.', $audioFile->format ?: 'mp3')->toString();

        return response()->download($path, $filename);
    }
}
