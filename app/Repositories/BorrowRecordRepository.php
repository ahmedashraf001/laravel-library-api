<?php 
namespace App\Repositories;
use App\Interfaces\BorrowRecordRepositoryInterface;
use App\Models\BorrowRecord;
class BorrowRecordRepository implements BorrowRecordRepositoryInterface
{
    private BorrowRecord $borrowRecord;
    public function __construct(BorrowRecord $borrowRecord)
    {
        $this->borrowRecord = $borrowRecord;
    }
    public function getHistory()
    {
     $borrowRecords = $this->borrowRecord->with(['book' => function($query) {
                                    $query->withTrashed()->select('id', 'title');}
                                     ])->orderBy('borrow_at', 'desc')->get();
        return $borrowRecords;
    }
    public function find(int $id)
    {
        $borrowRecord = $this->borrowRecord->find($id);
        return $borrowRecord;
    }
    public function create(string $userName, int $bookId)
    {
        $borrowRecord = $this->borrowRecord->create([
            'user_name' => $userName,
            'book_id' => $bookId,
            'borrow_at' => now(),
         ]);
        return $borrowRecord;
    }
    public function update(int $borrowId)
    {
        $borrowRecord = $this->borrowRecord->find($borrowId);
        $borrowRecord->update([ 'return_at' => now() ]);
        return $borrowRecord;
    }
    public function isBookBorrowed(int $bookId)
    {
        $isBorrowed = $this->borrowRecord->where('book_id',$bookId)
                                ->whereNull('return_at')
                                ->exists();
        return $isBorrowed;
    }
}