<?php
namespace App\DTOs;
use App\Http\Requests\StoreBookRequest;

readonly class StoreBookDTO{
    public function __construct(
        public string $title,
        public string $description,
        public int $year,
        public int $author_id,
    ) {}
    
    public static function fromRequest(StoreBookRequest $request) : self
    {
        $data = $request->validated();
        return new self(
            title: $data['title'],
            description: $data['description'],
            year: $data['year'],
            author_id: $data['author_id'],
        );
    }
    public function toArray():array
    {
        return[
            'title' => $this->title,
            'description' => $this->description,
            'year' => $this->year,
            'author_id' => $this->author_id,
        ];
    }


}