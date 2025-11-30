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
use App\Exceptions\BookNotFoundException;
class BorrowController extends Controller
{
    private BorrowingService $borrowingService;
    public function __construct(BorrowingService $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }
    public function borrow(BorrowRequest $request)
    {
            //422 Validation error(Form Request handled) or book already borrowed(Business Rule)
            try {
                $borrowRecord = $this->borrowingService->borrow($request->user_name, $request->book_id);
                return response()->json([
                    'message' => 'Book borrowed successfully',
                ], 200);
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
            $borrowRecord = $this->borrowingService->return($borrowId);
            return response()->json([
                'message' => 'Book returned successfully',
            ], 200); 
        }catch(BookNotFoundException $e){
            //404 Resource not found
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
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

    public function history(){

        try {
            // 200 Successful response
            $borrowRecords = $this->borrowingService->history();
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
