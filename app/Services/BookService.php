<?php
namespace App\Services;
use App\Models\Book;
use App\Exceptions\BookNotFoundException;
class BookService
{
    public function index(array $data)
    {
        $per_page = $data['per_page'] ?? 15;
        $books = Book::with('author:id,name')->paginate($per_page);
        // 200 Successful response
        // Return collection of books
        return $books;
    }
    public function show(int $id)
    {
        $book = Book::find($id);
        $this->ensureBookExists($book);

        return $book;
    }
    public function store(array $data)
    {
        $book = Book::create($data);
        return $book;
    }
    public function update(int $id, array $data)
    {
        $book = Book::find($id);
        $this->ensureBookExists($book);

        $book->update($data);
        return $book;
    }
    public function destroy(int $id)
    {
        $book = Book::find($id);
        $this->ensureBookExists($book);
        $book->delete();
        return $book;
    }


    public function ensureBookExists(?Book $book) 
    {
        if(!$book){
            throw new BookNotFoundException('Resource not found.');
        }
    }
}