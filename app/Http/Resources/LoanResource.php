<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->pivot?->id,
            'user' => $this->pivot?->user ? [
                'id' => $this->pivot->user->id,
                'name' => $this->pivot->user->name,
                'email' => $this->pivot->user->email,
            ] : null,
            'book' => [
                'id' => $this->id,
                'title' => $this->title,
                'author' => $this->author,
                'published_year' => $this->published_year,
                'isbn' => $this->isbn,
            ],
            // 'book' => new BookResource($this->book), // relasi ke Book
            'loaned_at' => $this->pivot?->loaned_at,
            'returned_at' => $this->pivot?->returned_at,
        ];
    }
}
