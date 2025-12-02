<?php

namespace Tests\Unit\Services;

use App\Services\BookService;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use App\Exceptions\BookNotFoundException;
use Mockery;
use Tests\TestCase;
use App\DTOs\BookListRequestDTO;
use App\DTOs\UpdateBookDTO;

class BookServiceTest extends TestCase
{
    private $mockRepository;
    private $bookService;

    /**
     * This method runs before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a fake (mock) repository
        $this->mockRepository = Mockery::mock(BookRepositoryInterface::class);
        
        // Create the service with our fake repository
        $this->bookService = new BookService($this->mockRepository);
    }

    /**
     * Test 1: index() should return paginated books
     */
    public function test_index_returns_books_from_repository()
    {
        // Arrange: Create fake book data
        $fakeBooks = collect([
            new Book(['id' => 1, 'title' => '1984', 'author_id' => 1]),
            new Book(['id' => 2, 'title' => 'Animal Farm', 'author_id' => 1]),
        ]);

        $dto = new BookListRequestDTO(per_page: 15);

        // Tell fake repository what to return
        $this->mockRepository
            ->shouldReceive('paginate')
            ->once()
            ->with($dto->per_page)
            ->andReturn($fakeBooks);
        
        // Act: Call the service
        $result = $this->bookService->index($dto);
        
        // Assert: Verify result
        $this->assertEquals($fakeBooks, $result);
    }

    /**
     * Test 2: update() should update book successfully
     */
    public function test_update_updates_book_successfully()
    {
        // Arrange: Prepare existing book and update data
        $existingBook = new Book([
            'id' => 1,
            'title' => 'Old Title',
            'description' => 'Old Description',
            'year' => 2000,
            'author_id' => 1
        ]);
        
        $dto = new UpdateBookDTO(
            title: 'New Title',
            description: 'New Description',
            year: 2023,
            author_id: 1
        );
        
        $updatedBook = new Book([
            'id' => 1,
            'title' => 'New Title',
            'description' => 'New Description',
            'year' => 2023,
            'author_id' => 1
        ]);
        
        // Tell fake repository: book exists
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($existingBook);
        
        // Tell fake repository: update should succeed
        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with(1, $dto->toArray())
            ->andReturn($updatedBook);
        
        // Act: Update the book
        $result = $this->bookService->update(1, $dto);
        
        // Assert: Check updated book is returned
        $this->assertEquals('New Title', $result->title);
        $this->assertEquals(2023, $result->year);
    }

    /**
     * Test 3: destroy() should delete book successfully
     */
    public function test_destroy_deletes_book_successfully()
    {
        // Arrange: Create fake book
        $book = new Book([
            'id' => 1,
            'title' => 'Book to Delete',
            'author_id' => 1
        ]);
        
        // Tell fake repository: book exists
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($book);
        
        // Tell fake repository: delete should succeed
        $this->mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);
        
        // Act: Delete the book
        $result = $this->bookService->destroy(1);
        
        // Assert: Check we got the book back
        $this->assertEquals($book, $result);
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

