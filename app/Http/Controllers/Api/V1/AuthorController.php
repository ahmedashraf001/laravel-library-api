<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;
use App\Http\Requests\StoreAuthorRequest;
use App\Models\Author;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\IndexAuthorsRequest;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\AuthorHasBooksException;
use App\Services\AuthorService;
class AuthorController extends Controller
{
    private AuthorService $authorService;
    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(IndexAuthorsRequest $request)
    {
        try {
            return $this->authorService->index($request);
            // 422 Validation error is automatically handled by IndexAuthorsRequest
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
    public function store(StoreAuthorRequest $request)
    {
        try {
            return $this->authorService->store($request->validated());
            // 422 Validation error is automatically handled by StoreAuthorRequest
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
        try{
            return $this->authorService->show($id);
        }
        catch (AuthorNotFoundException $e) {
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
    public function update(UpdateAuthorRequest $request, int $id)
    {
        try {
            return $this->authorService->update($id, $request->validated());

            // 422 Validation error
            // has been handled by the UpdateAuthorRequest class
         } catch (AuthorNotFoundException $e) {
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
            return $this->authorService->destroy($id);
        } 
        catch (AuthorNotFoundException $e) {
            // 404 Resource not found
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 404);
        }
        catch (AuthorHasBooksException $e) {
            // 409 Conflict - Cannot delete author with associated books
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 409);
        }
        catch (\Exception $e) {
           // 500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request',
                'errors' => []
            ], 500);
        }
    }   
}
