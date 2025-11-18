<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;
use App\Http\Requests\StoreAuthorRequest;
use App\Models\Author;
use Illuminate\Support\Facades\Log;
class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get pagination parameters from request, with defaults
        $perPage = $request->input('per_page', 15);
        
        // Validate per_page to ensure it's reasonable (1-100) limits
        if ($perPage > 100) $perPage = 100;
        if ($perPage < 1) $perPage = 15;
        
        // Paginate authors
        $authors = Author::paginate($perPage);
        
        return AuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        try {
            // 422 Validation error is automatically handled by StoreAuthorRequest
            
            // Create the author using validated data
            $author = Author::create($request->validated());
            
            // 201 Author created successfully
            return (new AuthorResource($author))
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
        $author = Author::find($id);
        if(!$author){
            return response()->json([
                'message' => 'Resource not found.',
                'errors' => [
                    'id' => [
                        'The requested resource does not exist.',
                    ]
                ],
            ], 404);
        }
        return new AuthorResource($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, int $id)
    {
        try {
            $author = Author::find($id);

            // 404 Resource not found
            if (! $author) {
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
            // has been handled by the UpdateAuthorRequest class

            // 200 Author updated successfully
            $author->update($request->validated());

            return new AuthorResource($author);
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
            $author= Author::find($id);
            // 404 Author not found
            if(!$author){
                return response()->json([
                    'message' => 'Resource not found',
                    'errors' => [
                        'id' => ['The requested resource does not exist']
                    ]
                ], 404);
            }

            // 409 Conflict - Cannot delete author with associated books
            if($author->books->count()){
                return response()->json([
                    'message' => 'Cannot delete author with associated books',
                    'errors' => [
                        'author' => ['This author has books and cannot be deleted']
                    ]
                ], 409);

            }

            // 204 No Content - Author deleted successfully
            $author->delete();
            return response()->noContent();

            // 500 Internal Server Error
        } catch (\Exception $e) {
          
            return response()->json([
                'message' => 'An error occurred while processing your request',
                'errors' => []
            ], 500);
        }
    }
}
