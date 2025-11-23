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
                return $this->bookService->index($request);
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
            return $this->bookService->store($request->validated());
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
            return $this->bookService->show($id);
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
           return $this->bookService->update($id, $request->validated());
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
            return $this->bookService->destroy($id);
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
