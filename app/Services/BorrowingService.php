<?php

namespace App\Services;

use App\Models\BorrowRecord;
use App\Exceptions\BookAlreadyBorrowedException;
use App\Exceptions\BookAlreadyReturnedException;
use App\Exceptions\BookNotFoundException;   
use App\Http\Resources\BorrowRecordResource;
class BorrowingService
{
   public function borrow(string $userName, int $bookId){
    // book already borrowed(Business Rule) -> throw custom Exception
    $isBorrowed = BorrowRecord::where('book_id',$bookId)
                                ->whereNull('return_at')
                                ->exists();

    if($isBorrowed){
        throw new BookAlreadyBorrowedException('The book is already borrowed');
    }

     // create borrow record
     $borrowRecord = BorrowRecord::create([
        'user_name' => $userName,
        'book_id' => $bookId,
        'borrow_at' => now(),
     ]);

    return response()->json([
        'message' => 'Book borrowed successfully',
    ], 200);

    }

   public function return(int $borrowId){

        $borrowRecord = BorrowRecord::find($borrowId);
        if(!$borrowRecord){
            throw new BookNotFoundException('Resource not found.');
        }
    
         if($borrowRecord->return_at){
            throw new BookAlreadyReturnedException(
                'This book has already been returned.',
                $borrowRecord->return_at
            );
        }
        $borrowRecord->update([ 'return_at' => now() ]);

        return response()->json([
            'message' => 'Book returned successfully',
        ], 200);  
     }
   
   public function history( ){
        // Include soft-deleted books in history
        $borrowRecords = BorrowRecord::with(['book' => function($query) {
            $query->withTrashed()->select('id', 'title');
        }])->orderBy('borrow_at', 'desc')->get();
        return BorrowRecordResource::collection($borrowRecords);
   }
}