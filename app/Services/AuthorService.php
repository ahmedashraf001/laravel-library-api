<?php
namespace App\Services;
use App\Models\Author;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\AuthorHasBooksException;
use App\Interfaces\AuthorRepositoryInterface;
use App\Repositories\AuthorRepository;
 class AuthorService
{
    private AuthorRepositoryInterface $authorRepository;
    public function __construct(AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function index(array $data)
    {
        $per_page = $data['per_page'] ?? 15;
        $authors = $this->authorRepository->paginate($per_page);

        // 200 Successful response
        // Return collection of authors
        return $authors;
    }
    public function show(int $id) :Author
    {
        $author = $this->authorRepository->find($id);
        // using guard clause(private method) to improve code readability and maintainability.
        $this->ensureAuthorExists($author);
        return $author;
    }
    public function store(array $data) :Author
    {
        $author = $this->authorRepository->create($data);
        // 200 Author created successfully
        return $author;
    }
    public function update(int $id, array $data)
    {
        $author = $this->authorRepository->find($id);
        // using guard clause(private method) to improve code readability and maintainability.
        $this->ensureAuthorExists($author);

        $author = $this->authorRepository->update($id , $data);
        // 200 Author updated successfully
        return $author;
    }
    public function destroy(int $id)
    {
        $author = $this->authorRepository->find($id);

        // using guard clause(private method) to improve code readability and maintainability.
        $this->ensureAuthorExists($author);
        $this->ensureAuthorHasNoBooks($author);
       
        $this->authorRepository->delete($id);
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