<?php
namespace App\Services;
use App\Models\Author;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\AuthorHasBooksException;
 class AuthorService
{
    public function index(array $data)
    {
        $per_page = $data['per_page'] ?? 15;
        // Fetch authors with books relationship (only fetch id and name for performance)
        $authors = Author::with('books:id,title')->paginate($per_page);
        // 200 Successful response
        // Return collection of authors
        return $authors;
    }
    public function show(int $id) :Author
    {
        $author = Author::find($id);
        // using guard clause(private method) to improve code readability and maintainability.
        $this->ensureAuthorExists($author);
        return $author;
    }
    public function store(array $data) :Author
    {
        $author = Author::create($data);
        // 200 Author created successfully
        return $author;
    }
    public function update(int $id, array $data)
    {
        $author = Author::find($id);
        // using guard clause(private method) to improve code readability and maintainability.
        $this->ensureAuthorExists($author);

        $author->update($data);
        // 200 Author updated successfully
        return $author;
    }
    public function destroy(int $id)
    {
        $author = Author::find($id);

        // using guard clause(private method) to improve code readability and maintainability.
        $this->ensureAuthorExists($author);
        $this->ensureAuthorHasNoBooks($author);
       
        $author->delete();
        // 204 Author deleted successfully
        return $author;
    }


    public function ensureAuthorExists(?Author $author) 
    {
        if(!$author){
            throw new AuthorNotFoundException('Resource not found.');
        }
    }
    public function ensureAuthorHasNoBooks(Author $author) 
    {
        if($author->books->count()){
            throw new AuthorHasBooksException('Cannot delete author with associated books');
        }
    }
}