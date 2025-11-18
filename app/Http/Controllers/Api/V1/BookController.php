<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            // Fetch books with author relationship  
            $books = Book::with('author:id,name')->get(); // only fetch the id and name of the author for performance  
            // Return collection of books
            return BookResource::collection($books);       
         
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        try {
            // 422 Validation error is automatically handled by StoreBookRequest
            
            // Create the book using validated data
            $book = Book::create($request->validated());
            
            // Load the author relationship
            $book->load('author:id,name');
            
            // 201 Book created successfully
            return (new BookResource($book))
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            // 500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) [],
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $book = Book::with('author:id,name')->find($id);
           
            if(!$book){
                // 404 Resource not found
                return response()->json([
                    'message' => 'Resource not found.',
                    'errors' => [
                        'id' => ['The requested resource does not exist.']
                    ]
                ], 404);
                    
            }
            
            // 200 Book found successfully
            return new BookResource($book);
            
        } catch (\Exception $e) {
            // 500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) []
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, int $id)
    {
        try {
            $book = Book::find($id);

            // 404 Resource not found
            if (!$book) {
                return response()->json([
                    'message' => 'Resource not found.',
                    'errors' => [
                        'id' => [
                            'The requested resource does not exist.',
                        ]
                    ],
                ], 404);
            }

            // 422 Validation error
            // has been handled by the UpdateBookRequest class

            // 200 Book updated successfully
            $book->update($request->validated());
            
            // Load the author relationship
            $book->load('author:id,name');

            return new BookResource($book);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) []
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                // 404 Book not found
                return response()->json([
                    'message' => 'Resource not found.',
                    'errors' => [
                        'id' => ['The requested resource does not exist.']
                    ]
                ], 404);
            }

            // Delete the book
            $book->delete();
            
            // 204 No Content - Book deleted successfully
            return response()->noContent();
            
      
        } catch (\Exception $e) {
            // 500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) []
            ], 500);
        }
    }
}
