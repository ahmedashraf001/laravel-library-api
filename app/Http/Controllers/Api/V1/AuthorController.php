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
use App\DTOs\AuthorListRequestDTO;
use App\DTOs\StoreAuthorDTO;
use App\DTOs\UpdateAuthorDTO;
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
            $dto = AuthorListRequestDTO::fromRequest($request);
            $authors = $this->authorService->index($dto);
            return AuthorResource::collection($authors);
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
            $dto = StoreAuthorDTO::fromRequest($request);
            $author = $this->authorService->store($dto);
            return response()->json([
                'message' => 'Author created successfully',
            ], 200);
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
            $author = $this->authorService->show($id);
            return new AuthorResource($author);
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
            $dto = UpdateAuthorDTO::fromRequest($request);
            $author = $this->authorService->update($id, $dto);
            return  response()->json([
                'message' => 'Author updated successfully',
            ], 200);

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
            $author= $this->authorService->destroy($id);
            return response()->noContent();
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
