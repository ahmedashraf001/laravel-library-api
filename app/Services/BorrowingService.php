<?php

namespace App\Services;

use App\Models\BorrowRecord;
use App\Exceptions\BookAlreadyBorrowedException;
use App\Exceptions\BookAlreadyReturnedException;
use App\Exceptions\BookNotFoundException;   
class BorrowingService
{
   public function borrow(string $userName, int $bookId) : BorrowRecord {
    // book already borrowed(Business Rule) -> throw custom Exception
    $this->ensureBookIsNotBorrowed($bookId);

     // create borrow record
     $borrowRecord = BorrowRecord::create([
        'user_name' => $userName,
        'book_id' => $bookId,
        'borrow_at' => now(),
     ]);

    return $borrowRecord;

    }

   public function return(int $borrowId){

        $borrowRecord = BorrowRecord::find($borrowId);

        $this->ensureBorrowRecordExists($borrowRecord);
        $this->ensureBorrowRecordIsNotReturned($borrowRecord);

        $borrowRecord->update([ 'return_at' => now() ]);

        return $borrowRecord;
     }
   
   public function history( ){
        // Include soft-deleted books in history
        $borrowRecords = BorrowRecord::with(['book' => function($query) {
            $query->withTrashed()->select('id', 'title');
        }])->orderBy('borrow_at', 'desc')->get();
        return $borrowRecords;
   }


   public function ensureBookIsNotBorrowed(int $bookId){
    $isBorrowed = BorrowRecord::where('book_id',$bookId)
                                ->whereNull('return_at')
                                ->exists();

    if($isBorrowed){
        throw new BookAlreadyBorrowedException('The book is already borrowed');
    }
   }
   public function ensureBorrowRecordExists(?BorrowRecord $borrowRecord) 
   {
    if(!$borrowRecord){
        throw new BookNotFoundException('Resource not found.');
    }
   }
   public function ensureBorrowRecordIsNotReturned(BorrowRecord $borrowRecord)
   {
    if($borrowRecord->return_at){
        throw new BookAlreadyReturnedException(
            'This book has already been returned.',
            $borrowRecord->return_at
        );
    }
   }

}