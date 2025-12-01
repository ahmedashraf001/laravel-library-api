<?php

namespace Tests\Unit\Services;

use App\Services\BorrowingService;
use App\Interfaces\BorrowRecordRepositoryInterface;
use App\Models\BorrowRecord;
use App\Exceptions\BookAlreadyBorrowedException;
use App\Exceptions\BookAlreadyReturnedException;
use App\Exceptions\BookNotFoundException;
use Mockery;
use Tests\TestCase;

class BorrowingServiceTest extends TestCase
{
    private $mockRepository;
    private $borrowingService;

    /**
     * This method runs before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a fake (mock) repository
        $this->mockRepository = Mockery::mock(BorrowRecordRepositoryInterface::class);
        
        // Create the service with our fake repository
        $this->borrowingService = new BorrowingService($this->mockRepository);
    }

    /**
     * Test 1: borrow() should create borrow record when book is available
     */
    public function test_borrow_creates_record_when_book_is_available()
    {
        // Arrange: Prepare data
        $userName = 'Ahmed Ashraf';
        $bookId = 1;
        
        $fakeBorrowRecord = new BorrowRecord([
            'id' => 1,
            'user_name' => $userName,
            'book_id' => $bookId,
            'borrow_at' => now(),
            'return_at' => null
        ]);

        // Tell fake repository: book is NOT borrowed
        $this->mockRepository
            ->shouldReceive('isBookBorrowed')
            ->once()
            ->with($bookId)
            ->andReturn(false); // Book is available!

        // Tell fake repository: create should succeed
        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($userName, $bookId)
            ->andReturn($fakeBorrowRecord);

        // Act: Borrow the book
        $result = $this->borrowingService->borrow($userName, $bookId);

        // Assert: Check we got the borrow record
        $this->assertEquals($fakeBorrowRecord, $result);
        $this->assertEquals($userName, $result->user_name);
        $this->assertEquals($bookId, $result->book_id);
    }

    /**
     * Test 2: borrow() should throw exception when book is already borrowed
     */
    public function test_borrow_throws_exception_when_book_already_borrowed()
    {
        // Arrange: Tell fake repository the book is already borrowed
        $this->mockRepository
            ->shouldReceive('isBookBorrowed')
            ->once()
            ->with(1)
            ->andReturn(true); // Book is already borrowed!

        // Assert: Expect exception
        $this->expectException(BookAlreadyBorrowedException::class);
        $this->expectExceptionMessage('The book is already borrowed');

        // Act: Try to borrow already borrowed book
        $this->borrowingService->borrow('Ahmed', 1);
    }

    /**
     * Test 3: return() should update record when book is borrowed
     */
    public function test_return_updates_record_when_book_is_borrowed()
    {
        // Arrange: Create fake borrow record (not yet returned)
        $borrowRecord = new BorrowRecord([
            'id' => 1,
            'user_name' => 'Ahmed',
            'book_id' => 1,
            'borrow_at' => now()->subDays(2),
            'return_at' => null // Not returned yet
        ]);
        
        $returnedRecord = new BorrowRecord([
            'id' => 1,
            'user_name' => 'Ahmed',
            'book_id' => 1,
            'borrow_at' => now()->subDays(2),
            'return_at' => now() // Now returned
        ]);

        // Tell fake repository: record exists
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($borrowRecord);

        // Tell fake repository: update should succeed
        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with(1)
            ->andReturn($returnedRecord);

        // Act: Return the book
        $result = $this->borrowingService->return(1);

        // Assert: Check we got updated record
        $this->assertEquals($returnedRecord, $result);
    }

    /**
     * Test 4: return() should throw exception when book already returned
     */
    public function test_return_throws_exception_when_already_returned()
    {
        // Arrange: Create fake record that's already returned
        $alreadyReturnedRecord = new BorrowRecord([
            'id' => 1,
            'user_name' => 'Ahmed',
            'book_id' => 1,
            'borrow_at' => now()->subDays(2),
            'return_at' => now()->subDay() // Already returned!
        ]);

        // Tell fake repository: record exists and is already returned
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($alreadyReturnedRecord);

        // Assert: Expect exception
        $this->expectException(BookAlreadyReturnedException::class);

        // Act: Try to return already returned book
        $this->borrowingService->return(1);
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

