<?php

namespace App\Services;

use App\DTOs\BorrowRequestDTO;
use App\Models\BorrowRecord;
use App\Exceptions\BookAlreadyBorrowedException;
use App\Exceptions\BookAlreadyReturnedException;
use App\Exceptions\BookNotFoundException;  
use App\Interfaces\BorrowRecordRepositoryInterface;
class BorrowingService
{
    private BorrowRecordRepositoryInterface $borrowRecordRepository;
    public function __construct(BorrowRecordRepositoryInterface $borrowRecordRepository)
    {
        $this->borrowRecordRepository = $borrowRecordRepository;
    }
    
    public function borrow(BorrowRequestDTO $dto) : BorrowRecord {
    // book already borrowed(Business Rule) -> throw custom Exception
    $this->ensureBookIsNotBorrowed($dto->book_id);

     // create borrow record
     $borrowRecord = $this->borrowRecordRepository->create($dto->user_name, $dto->book_id);

    return $borrowRecord;

    }

   public function return(int $borrowId){

        $borrowRecord = $this->borrowRecordRepository->find($borrowId);

        $this->ensureBorrowRecordExists($borrowRecord);
        $this->ensureBorrowRecordIsNotReturned($borrowRecord);

        $borrowRecord = $this->borrowRecordRepository->update($borrowId);       
        return $borrowRecord;
     }
   
   public function history( ){
        // Include soft-deleted books in history
        $borrowRecords = $this->borrowRecordRepository->getHistory();
        return $borrowRecords;
   }


   public function ensureBookIsNotBorrowed(int $bookId){
    $isBorrowed = $this->borrowRecordRepository->isBookBorrowed($bookId);

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