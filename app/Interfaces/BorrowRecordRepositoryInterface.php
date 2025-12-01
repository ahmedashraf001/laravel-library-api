<?php
namespace App\Interfaces;
use App\Models\BorrowRecord;
interface BorrowRecordRepositoryInterface
{
   public function getHistory();
   public function find(int $id);
   public function create(string $userName, int $bookId);
   public function update(int $borrowId);
   public function isBookBorrowed(int $bookId);
 }