<?php
namespace App\Exceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class BookAlreadyReturnedException extends Exception 
{
    protected $returnDate;
    
    public function __construct(string $message = "This book has already been returned.", $returnDate = null)
    {
        parent::__construct($message);
        $this->returnDate = $returnDate;
    }
    
    public function errors(): array
    {
        $formattedDate = 'an unknown date';
        
        if ($this->returnDate) {
            if ($this->returnDate instanceof Carbon) {
                $formattedDate = $this->returnDate->format('Y-m-d');
            } else {
                $formattedDate = Carbon::parse($this->returnDate)->format('Y-m-d');
            }
        }
        
        return [
            'borrow_id' => [
                'The book was already returned on ' . $formattedDate . '.'
            ]
        ];
    }
    
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->errors()
        ], 422);
    }
}