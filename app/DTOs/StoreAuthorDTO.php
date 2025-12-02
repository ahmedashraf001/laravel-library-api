<?php
namespace App\DTOs;
use App\Http\Requests\StoreAuthorRequest;

readonly class StoreAuthorDTO
{
    public function __construct(
        public string $name,
        public ?string $bio,
        public string $dob,
    ) {}
    
    public static function fromRequest(StoreAuthorRequest $request) : self
    {
        $data = $request->validated();
        return new self(
                name:$data['name'],
                bio: $data['bio'] ?? null,
                dob: $data['dob'],
            );
    }

    public function toArray():array
    {
        return [
            // with the db column names
            'name' => $this->name,
            'bio' => $this->bio,
            'dob' => $this->dob,
        ];
    }
 }