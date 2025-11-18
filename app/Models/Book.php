<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Add SoftDeletes 

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory, SoftDeletes; // Add SoftDeletes trait

    protected $fillable = ['title', 'description', 'year', 'author_id'];

    public function author(){
        return $this->belongsTo(Author::class);
    }

    public function borrowRecords(){
        return $this->hasMany(BorrowRecord::class);
    }
    
}
