<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\IndexBooksRequest;
use App\Services\BookService;
use App\Exceptions\BookNotFoundException;
class BookController extends Controller
{
    private BookService $bookService;
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(IndexBooksRequest $request)
    {
            try{
                $books = $this->bookService->index($request->validated());
                return BookResource::collection($books);
                // 422 Validation error is automatically handled by IndexBooksRequest
            } catch (\Exception $e) {
                // 500 Internal Server Error
                return response()->json([
                    'message' => 'An error occurred while processing your request.',
                    'errors' => (object) [],
                ], 500);
            }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        try {
            $book = $this->bookService->store($request->validated());
            return response()->json([
                'message' => 'Book created successfully',
            ], 200);
            // 422 Validation error is automatically handled by StoreBookRequest
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
            $book = $this->bookService->show($id);
            return new BookResource($book);
        }
        catch (BookNotFoundException $e) {
            // 404 Resource not found
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 404);
        }
        catch (\Exception $e) {
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
           $book= $this->bookService->update($id, $request->validated());
           return response()->json([
            'message' => 'Book updated successfully',
        ], 200);
            // 422 Validation error
            // has been handled by the UpdateBookRequest class
        }
        catch (BookNotFoundException $e) {
            // 404 Resource not found
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 404);
        }
        catch (\Exception $e) {
            // 500 Internal Server Error
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
            $book= $this->bookService->destroy($id);
            return response()->noContent();
        }
        catch (BookNotFoundException $e) {
            // 404 Resource not found
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 404);
        }
        catch (\Exception $e) {
            // 500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) []
            ], 500);
        }
    }
}
