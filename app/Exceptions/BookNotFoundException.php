<?php
namespace App\Exceptions;
use Exception;
class BookNotFoundException extends Exception
{
    public function errors() :array
    {
        return [
            'book_id' => ['The requested resource does not exist.'],
        ];
    }
}