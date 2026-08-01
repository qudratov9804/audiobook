<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => $this->author,
            'description' => $this->description,
            'status' => $this->status,
            'duration' => $this->duration,
            'cover_url' => $this->cover_path ? asset('storage/'.$this->cover_path) : null,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'user' => new UserResource($this->whenLoaded('user')),
            'audio_file' => new AudioFileResource($this->whenLoaded('audioFile')),
            'transcript' => new TranscriptResource($this->whenLoaded('finalTranscript')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
