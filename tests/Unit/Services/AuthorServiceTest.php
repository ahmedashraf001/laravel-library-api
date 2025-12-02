<?php

namespace Tests\Unit\Services;

use App\Services\AuthorService;
use App\Interfaces\AuthorRepositoryInterface;
use App\Models\Author;
use App\Exceptions\AuthorNotFoundException;
use App\Exceptions\AuthorHasBooksException;
use Mockery;
use Tests\TestCase;
use App\DTOs\AuthorListRequestDTO;
use App\DTOs\StoreAuthorDTO;

class AuthorServiceTest extends TestCase
{
    private $mockRepository;
    private $authorService;

    /**
     * This method runs before each test
     * We set up our mock repository and service here
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a fake (mock) repository that we can control
        $this->mockRepository = Mockery::mock(AuthorRepositoryInterface::class);
        
        // Create the service with our fake repository
        $this->authorService = new AuthorService($this->mockRepository);
    }

    /**
     * Test 1: index() should return paginated authors from repository
     */
    public function test_index_returns_authors_from_repository()
    {
        // Arrange: Create fake author data
        $fakeAuthors = collect([
            new Author(['id' => 1, 'name' => 'J.K. Rowling']),
            new Author(['id' => 2, 'name' => 'George Orwell']),
        ]);

        // Create DTO directly using constructor
        $dto = new AuthorListRequestDTO(per_page: 15);
        // Tell our fake repository: "When paginate(15) is called, return fake authors"
        $this->mockRepository
            ->shouldReceive('paginate')
            ->once() // Should be called exactly once
            ->with($dto->per_page) // With parameter 15
            ->andReturn($fakeAuthors); // Return our fake data
        
        // Act: Call the service method
        $result = $this->authorService->index($dto);
        
        // Assert: Check we got the expected result
        $this->assertEquals($fakeAuthors, $result);
    }

    
    /**
     * Test 2: store() should create author successfully
     */
    public function test_store_creates_author_successfully()
    {
        // Arrange: Prepare data and expected result
        $dto = new StoreAuthorDTO(
            name: 'Neil Gaiman',
            bio:'Author of fantasy novels',
            dob: '1960-11-10'
        );
        
        $createdAuthor = new Author([
            'id' => 1,
            'name' => 'Neil Gaiman',
            'bio' => 'Author of fantasy novels',
            'dob' => '1960-11-10'
        ]);
        
        // Tell fake repository what to do when create() is called
        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andReturn($createdAuthor);
        
        // Act: Call store method
        $result = $this->authorService->store($dto);
        
        // Assert: Check we got the created author
        $this->assertEquals($createdAuthor, $result);
        $this->assertEquals('Neil Gaiman', $result->name);
    }

    /**
     * Test 3: show() should return author successfully
     */
    public function test_show_returns_author_successfully()
    {
        // Arrange: Create fake author
        $fakeAuthor = new Author([
            'id' => 1,
            'name' => 'J.K. Rowling',
            'bio' => 'British author',
            'dob' => '1965-07-31'
        ]);
        
        // Tell fake repository: when find(1) is called, return the author
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($fakeAuthor);
        
        // Act: Call show method
        $result = $this->authorService->show(1);
        
        // Assert: Check we got the correct author
        $this->assertEquals($fakeAuthor, $result);
        $this->assertEquals('J.K. Rowling', $result->name);
    }

    /**
     * This method runs after each test
     * Clean up our mocks
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

