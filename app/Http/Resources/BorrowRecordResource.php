<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowRecordResource extends JsonResource
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
            'user_name' => $this->user_name,
            'book_id' => $this->book_id,
            // include book if it loaded via eager loading when borrow records are fetched
            'book' => $this->when($this->relationLoaded('book'), function() {
                return [
                    'id' => $this->book->id,
                    'title' => $this->book->title,
                ];
            }),
            'borrow_at' => $this->borrow_at,
            'return_at' => $this->return_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
     }
}
