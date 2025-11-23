<?php
namespace App\Services;
use App\Models\Author;
use App\Http\Resources\AuthorResource;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\AuthorHasBooksException;
use App\Http\Requests\IndexAuthorsRequest;
class AuthorService
{
    public function index(IndexAuthorsRequest $request)
    {
        $per_page = $request->input('per_page', 15);
        // Fetch authors with books relationship (only fetch id and name for performance)
        $authors = Author::with('books:id,title')->paginate($per_page);
        // 200 Successful response
        // Return collection of authors
        return AuthorResource::collection($authors);
    }
    public function show(int $id)
    {
        $author = Author::find($id);
        if(!$author){
            throw new AuthorNotFoundException('Resource not found.');
        }
        return new AuthorResource($author);
    }
    public function store(array $data)
    {
        $author = Author::create($data);
        // 200 Author created successfully
        return response()->json([
            'message' => 'Author created successfully',
        ], 200);
    }
    public function update(int $id, array $data)
    {
        $author = Author::find($id);
        if(!$author){
            throw new AuthorNotFoundException('Resource not found.');
        }
        $author->update($data);
        // 200 Author updated successfully
        return response()->json([
            'message' => 'Author updated successfully',
        ], 200);
    }
    public function destroy(int $id)
    {
        $author = Author::find($id);
        if(!$author){
            throw new AuthorNotFoundException('Resource not found.');
        }
        if($author->books->count()){
            throw new AuthorHasBooksException('Cannot delete author with associated books');
        }

        $author->delete();
        // 204 Author deleted successfully
        return response()->noContent();
    }
}