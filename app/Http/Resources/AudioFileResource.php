<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudioFileResource extends JsonResource
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
            'format' => $this->format,
            'duration' => $this->duration,
            'size' => $this->size,
            'status' => $this->status,
            'chunk_count' => $this->whenCounted('chunks'),
            'created_at' => $this->created_at,
        ];
    }
}
