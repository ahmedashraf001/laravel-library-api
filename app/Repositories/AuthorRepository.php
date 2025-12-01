<?php
namespace App\Repositories;
use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;
class AuthorRepository implements AuthorRepositoryInterface
{
    private Author $author;
    public function __construct(Author $author){
        $this->author = $author;
    }

    public function paginate(int $per_page)
    {
        // Fetch authors with books relationship (only fetch id and name for performance)
        $authors = $this->author->with('books:id,title')->paginate($per_page);
        return $authors;
    }
    public function find(int $id)
    {
        return $this->author->find($id);
    }
    public function create(array $data)
    {
        return $this->author->create($data);
    }
    public function update(int $id, array $data)
    {
        $author = $this->author->find($id);
        $author->update($data);
        return $author;
    }
    public function delete(int $id)
    {
        return $this->author->find($id)->delete();
    }
    public function isAuthorHasBooks(int $id)
    {
        $author = $this->author->find($id);
        return $author->books->count() > 0 ? true : false;
    }
}