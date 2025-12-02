<?php
namespace App\Services;
use App\Models\Book;
use App\Exceptions\BookNotFoundException;
use App\Interfaces\BookRepositoryInterface;
use App\DTOs\BookListRequestDTO;
use App\DTOs\StoreBookDTO;
use App\DTOs\UpdateBookDTO;
class BookService
{
    private BookRepositoryInterface $bookRepository;
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function index(BookListRequestDTO $dto)
    {
        $per_page = $dto->per_page;
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
    public function store(StoreBookDTO $dto)
    {
        $book = $this->bookRepository->create($dto->toArray());
        return $book;
    }
    public function update(int $id, UpdateBookDTO $dto)
    {
        $book = $this->bookRepository->find($id);
        $this->ensureBookExists($book);

        $book = $this->bookRepository->update($id ,$dto->toArray());
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