<?php

namespace App\Services;

use App\Models\BorrowRecord;
use App\Exceptions\BookAlreadyBorrowedException;
use App\Exceptions\BookAlreadyReturnedException;
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

     return  $borrowRecord->load('book');

    }

   public function return(BorrowRecord $borrowRecord){
    
         if($borrowRecord->return_at){
            throw new BookAlreadyReturnedException(
                'This book has already been returned.',
                $borrowRecord->return_at
            );
        }
        $borrowRecord->update([ 'return_at' => now() ]);

        return $borrowRecord->load('book');
   }
}