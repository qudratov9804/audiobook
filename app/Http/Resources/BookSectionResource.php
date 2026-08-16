<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'format' => $this->format,
            'duration' => $this->duration,
            'size' => $this->size,
            'url' => route('api.v1.books.sections.stream', [
                'book' => $this->book_id,
                'section' => $this->id,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
