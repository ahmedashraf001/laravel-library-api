<?php
namespace App\DTOs;
use App\Http\Requests\BorrowRequest;

readonly class BorrowRequestDTO
{

    public function __construct(
        public string $user_name,
        public int $book_id,
    ){}

    public static function fromRequest(BorrowRequest $request):self
    {
        $data = $request->validated();
        return new self(
            user_name: $data['user_name'],
            book_id: $data['book_id'],
        );
    }

    public function toArray():array
    {
        return[
            'user_name' => $this->user_name,
            'book_id' => $this->book_id,
        ];
    }
}