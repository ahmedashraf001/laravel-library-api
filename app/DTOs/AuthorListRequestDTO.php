<?php
namespace App\DTOs;
use App\Http\Requests\IndexAuthorsRequest;
readonly class AuthorListRequestDTO 
{
    public function __construct(
        public int $per_page = 15,
    ) {}
    
    public static function fromRequest(IndexAuthorsRequest $request): self
    {
        $data = $request->validated();
        return new self(
            per_page: $data['per_page'] ?? 15,
        );
    }
   
}