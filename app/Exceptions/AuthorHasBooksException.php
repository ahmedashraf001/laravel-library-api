<?php
namespace App\Exceptions;
use Exception;
class AuthorHasBooksException extends Exception
{
    public function errors() :array
    {
        return [
            'author' => ['This author has books and cannot be deleted'],
        ];
    }
}