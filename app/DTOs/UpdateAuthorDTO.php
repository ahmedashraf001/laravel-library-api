<?php
namespace App\DTOs;
use App\Http\Requests\UpdateAuthorRequest;
readonly class UpdateAuthorDTO
{
    public function __construct(
        public string $name,
        public ?string $bio,
        public string $dob,
    ) {}
    
    public static function fromRequest(UpdateAuthorRequest $request) : self
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
            'name' => $this->name,
            'bio' => $this->bio,
            'dob' => $this->dob,
        ];
    }
}