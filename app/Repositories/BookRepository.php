<?php
namespace App\Repositories;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    private Book $book;
    public function __construct(Book $book){
        $this->book = $book;
    }
    public function paginate(int $per_page)
    {
        $books = $this->book->with('author:id,name')->paginate($per_page);
        return $books;
    }
    public function find(int $id)
    {
        return $this->book->find($id);
    }
    public function create(array $data)
    {
        return $this->book->create($data);
    }
    public function update(int $id, array $data)
    {
        $book = $this->book->find($id);
        $book->update($data);
        return $book;
    }
    public function delete(int $id)
    {
        return $this->book->find($id)->delete();
    }
}