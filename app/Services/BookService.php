<?php
namespace App\Services;
use App\Models\Book;
use App\Exceptions\BookNotFoundException;
use App\Interfaces\BookRepositoryInterface;
class BookService
{
    private BookRepositoryInterface $bookRepository;
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function index(array $data)
    {
        $per_page = $data['per_page'] ?? 15;
        $books = $this->bookRepository->paginate($per_page);
        // 200 Successful response
        // Return collection of books
        return $books;
    }
    public function show(int $id)
    {
        $book = $this->bookRepository->find($id);
        $this->ensureBookExists($book);

        return $book;
    }
    public function store(array $data)
    {
        $book = $this->bookRepository->create($data);
        return $book;
    }
    public function update(int $id, array $data)
    {
        $book = $this->bookRepository->find($id);
        $this->ensureBookExists($book);

        $book = $this->bookRepository->update($id , $data);
        return $book;
    }
    public function destroy(int $id)
    {
        $book = $this->bookRepository->find($id);
        $this->ensureBookExists($book);
        $this->bookRepository->delete($id);
        return $book;
    }


    public function ensureBookExists(?Book $book) 
    {
        if(!$book){
            throw new BookNotFoundException('Resource not found.');
        }
    }
}