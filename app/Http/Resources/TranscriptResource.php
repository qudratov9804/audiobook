<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranscriptResource extends JsonResource
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
            'is_final' => $this->is_final,
            'format' => $this->format,
            'status' => $this->status,
            'content' => $this->when($request->boolean('with_content'), $this->content),
            'created_at' => $this->created_at,
        ];
    }
}
