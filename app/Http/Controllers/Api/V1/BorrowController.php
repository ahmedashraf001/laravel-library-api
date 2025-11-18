<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowRequest;
use Illuminate\Http\Request;
use App\Http\Requests\ReturnRequest;
use App\Exceptions\BookAlreadyBorrowedException;
use App\Exceptions\BookAlreadyReturnedException;
use App\Services\BorrowingService;
use App\Http\Resources\BorrowRecordResource;
use App\Models\BorrowRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class BorrowController extends Controller
{
    public function borrow(BorrowRequest $request)
    {
            //422 Validation error(Form Request handled) or book already borrowed(Business Rule)
            try {
                 
                $borrowRecord = (new BorrowingService())->borrow($request->user_name, $request->book_id);

                return (new BorrowRecordResource($borrowRecord))
                        ->response()
                        ->setStatusCode(201);

            }catch(BookAlreadyBorrowedException $e){
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }catch(\Exception $e){
                 //500 Internal Server Error
                return response()->json([
                    'message' => 'An error occurred while processing your request.',
                    'errors' => (object) []
                ], 500);
            }
          
            
    }

    public function return(int $borrowId){

        //422 Validation error(Form Request handled) or book already borrowed(Business Rule)
        try {
            $borrowRecord = BorrowRecord::findOrFail($borrowId);
            $borrowRecordreturned = (new BorrowingService())->return( $borrowRecord);

            return (new BorrowRecordResource( $borrowRecordreturned))
                    ->response()
                    ->setStatusCode(200);

        }catch(ModelNotFoundException $e){
            //404 Resource not found
            return response()->json([
                'message' => 'Resource not found.',
                'errors' => [
                    'id' => [
                        'The requested resource does not exist.'
                    ]
                ]
            ], 404);
        }catch(BookAlreadyReturnedException $e){
            //422 Book already returned
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        }catch(\Exception $e){
            //500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) []
            ], 500);
        }
    
    }

    public function history(Request $request){

        // 200 Successful response
        // 500 Internal Server Error
        try {
            $borrowRecords = BorrowRecord::with('book:id,title')->get();
            return BorrowRecordResource::collection($borrowRecords);

        } catch (\Exception $e) {
            //500 Internal Server Error
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'errors' => (object) []
            ], 500);
        }
    }
}
