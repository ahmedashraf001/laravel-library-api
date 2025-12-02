<?php
namespace App\DTOs;
use App\Http\Requests\IndexBooksRequest;

readonly class BookListRequestDTO{
    public function __construct(
        public int $per_page=15,
    ) {}

    public static function fromRequest(IndexBooksRequest $request): self
    {
        $data = $request->validated();
        return new self(
            per_page: $data['per_page'] ?? 15,
        );
    }
}