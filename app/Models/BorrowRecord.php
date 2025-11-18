<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowRecordFactory> */
    use HasFactory;
    protected $fillable = ['user_name', 'book_id', 'borrow_at', 'return_at'];
    
    public function book(){
        return $this->belongsTo(Book::class);
    }
}
