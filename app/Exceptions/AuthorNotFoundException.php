<?php
namespace App\Exceptions;
use Exception;
class AuthorNotFoundException extends Exception
{
    public function errors() :array
    {
        return [
            'author_id' => ['The requested resource does not exist.'],
        ];
    }
}