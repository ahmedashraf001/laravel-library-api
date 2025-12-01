<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================
// BORROW TESTS
// ============================

// Test 1: Successfully borrow a book
test('can borrow an available book with valid data', function () {
    // Arrange: Create an author and a book
    $author = Author::factory()->create(['name' => 'J.K. Rowling']);
    $book = Book::factory()->create([
        'title' => 'Harry Potter',
        'author_id' => $author->id
    ]);

    $borrowData = [
        'user_name' => 'Ahmed Ashraf',
        'book_id' => $book->id
    ];

    // Act: Send POST request to borrow
    $response = $this->postJson('/api/v1/borrow', $borrowData);

    // Assert: Check response and database
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Book borrowed successfully'
        ]);

    $this->assertDatabaseHas('borrow_records', [
        'user_name' => 'Ahmed Ashraf',
        'book_id' => $book->id,
        'return_at' => null
    ]);
});

// Test 2: Cannot borrow a book that is already borrowed
test('returns error when trying to borrow an already borrowed book', function () {
    // Arrange: Create a book and borrow it
    $author = Author::factory()->create();
    $book = Book::factory()->create(['author_id' => $author->id]);
    
    // First borrow
    BorrowRecord::create([
        'user_name' => 'First User',
        'book_id' => $book->id,
        'borrow_at' => now(),
        'return_at' => null
    ]);

    // Try to borrow the same book again
    $borrowData = [
        'user_name' => 'Second User',
        'book_id' => $book->id
    ];

    // Act: Send POST request
    $response = $this->postJson('/api/v1/borrow', $borrowData);

    // Assert: Check error response
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The book is already borrowed',
            'errors' => [
                'book_id' => ['This book is currently borrowed and unavailable.']
            ]
        ]);

    // Ensure only one borrow record exists
    $this->assertDatabaseCount('borrow_records', 1);
});

// ============================
// RETURN TESTS
// ============================

// Test 3: Successfully return a borrowed book
test('can return a borrowed book', function () {
    // Arrange: Create a book and borrow it
    $author = Author::factory()->create();
    $book = Book::factory()->create(['author_id' => $author->id]);
    
    $borrowRecord = BorrowRecord::create([
        'user_name' => 'Ahmed Ashraf',
        'book_id' => $book->id,
        'borrow_at' => now(),
        'return_at' => null
    ]);

    // Act: Send POST request to return
    $response = $this->postJson("/api/v1/return/{$borrowRecord->id}");

    // Assert: Check response and database
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Book returned successfully'
        ]);

    // Verify return_at is now set
    $this->assertDatabaseHas('borrow_records', [
        'id' => $borrowRecord->id,
        'user_name' => 'Ahmed Ashraf'
    ]);
    
    $this->assertNotNull(BorrowRecord::find($borrowRecord->id)->return_at);
});

// Test 4: Cannot return a book that is already returned
test('returns error when trying to return an already returned book', function () {
    // Arrange: Create a book, borrow it, and return it
    $author = Author::factory()->create();
    $book = Book::factory()->create(['author_id' => $author->id]);
    
    $borrowRecord = BorrowRecord::create([
        'user_name' => 'Ahmed Ashraf',
        'book_id' => $book->id,
        'borrow_at' => now()->subDays(2),
        'return_at' => now()->subDay() // Already returned
    ]);

    // Act: Try to return the book again
    $response = $this->postJson("/api/v1/return/{$borrowRecord->id}");

    // Assert: Check error response
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'This book has already been returned.'
        ])
        ->assertJsonStructure([
            'message',
            'errors' => ['borrow_id']
        ]);
});

// ============================
// HISTORY TESTS
// ============================

// Test 5: Can list all borrow records with book information
test('can list all borrow history with book information', function () {
    // Arrange: Create multiple borrow records
    $author = Author::factory()->create(['name' => 'J.K. Rowling']);
    $book1 = Book::factory()->create(['title' => 'Book 1', 'author_id' => $author->id]);
    $book2 = Book::factory()->create(['title' => 'Book 2', 'author_id' => $author->id]);
    
    // Create active borrow
    BorrowRecord::create([
        'user_name' => 'Ahmed Ashraf',
        'book_id' => $book1->id,
        'borrow_at' => now(),
        'return_at' => null
    ]);
    
    // Create returned borrow
    BorrowRecord::create([
        'user_name' => 'Sara Mohamed',
        'book_id' => $book2->id,
        'borrow_at' => now()->subDays(2),
        'return_at' => now()->subDay()
    ]);

    // Act: Send GET request
    $response = $this->getJson('/api/v1/history');

    // Assert: Check response
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_name',
                    'book_id',
                    'book' => ['id', 'title'],
                    'borrow_at',
                    'return_at',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
});

// Test 6: Returns empty array when no borrow records exist
test('returns empty array when no borrow history exists', function () {
    // Arrange: No borrow records created

    // Act: Send GET request
    $response = $this->getJson('/api/v1/history');

    // Assert: Check response returns empty data array
    $response->assertStatus(200)
        ->assertJsonCount(0, 'data')
        ->assertJsonStructure([
            'data'
        ]);
});
