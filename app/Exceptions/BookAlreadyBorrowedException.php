<?php
namespace App\Exceptions;
use Exception;

class BookAlreadyBorrowedException extends Exception {
    public function errors() :array {
        return [
            'book_id' => ['This book is currently borrowed and unavailable.'],
        ];
    }
}