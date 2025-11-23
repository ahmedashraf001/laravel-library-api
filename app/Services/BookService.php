<?php
namespace App\Services;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Requests\IndexBooksRequest;
use App\Exceptions\BookNotFoundException;
class BookService
{
    public function index(IndexBooksRequest $request)
    {
        $per_page = $request->input('per_page', 15);
        $books = Book::with('author:id,name')->paginate($per_page);
        // 200 Successful response
        // Return collection of books
        return BookResource::collection($books);
    }
    public function show(int $id)
    {
        $book = Book::find($id);
        if(!$book){
            throw new BookNotFoundException('Resource not found.');
        }
        return new BookResource($book);
    }
    public function store(array $data)
    {
        $book = Book::create($data);
        return response()->json([
            'message' => 'Book created successfully',
        ], 200);
    }
    public function update(int $id, array $data)
    {
        $book = Book::find($id);
        if(!$book){
            throw new BookNotFoundException('Resource not found.');
        }
        $book->update($data);
        return response()->json([
            'message' => 'Book updated successfully',
        ], 200);
    }
    public function destroy(int $id)
    {
        $book = Book::find($id);
        if(!$book){
            throw new BookNotFoundException('Resource not found.');
        }
        $book->delete();
        return response()->json([
            'message' => 'Book deleted successfully',
        ], 200);
    }
}