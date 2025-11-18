<?php

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Test 1: List Books with Author information
test('can list all books with author information', function () {
    // Arrange: Create an author and books
    $author = Author::factory()->create(['name' => 'J.K. Rowling']);
    Book::factory()->count(3)->create(['author_id' => $author->id]);

    // Act: Send GET request
    $response = $this->getJson('/api/v1/books');

    // Assert: Check response
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'year',
                    'author_id',
                    'author' => ['id', 'name'],
                    'created_at',
                    'updated_at'
                ]
            ]
        ])
        ->assertJsonPath('data.0.author.name', 'J.K. Rowling');
});

// Test 2: Create Book with valid data
test('can create a new book with valid data', function () {
    // Arrange: Create an author first
    $author = Author::factory()->create();
    
    $bookData = [
        'title' => 'American Gods',
        'description' => 'A blend of Americana, fantasy, and mythology',
        'year' => 2001,
        'author_id' => $author->id
    ];

    // Act: Send POST request
    $response = $this->postJson('/api/v1/books', $bookData);

    // Assert: Check response and database
    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'American Gods')
        ->assertJsonPath('data.year', 2001)
        ->assertJsonPath('data.author_id', $author->id)
        ->assertJsonPath('data.author.id', $author->id)
        ->assertJsonPath('data.author.name', $author->name);

    $this->assertDatabaseHas('books', [
        'title' => 'American Gods',
        'year' => 2001,
        'author_id' => $author->id
    ]);
});

// Test 3: Validation errors when creating book
test('returns validation errors when creating book with invalid author_id', function () {
    // Arrange: Invalid data (non-existent author_id)
    $invalidData = [
        'title' => 'Test Book',
        'description' => 'Test Description',
        'year' => 2020,
        'author_id' => 999 // Non-existent author
    ];

    // Act: Send POST request
    $response = $this->postJson('/api/v1/books', $invalidData);

    // Assert: Check validation error response
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'author_id' => ['The selected author id is invalid.']
            ]
        ]);

    // Ensure book was not created
    $this->assertDatabaseMissing('books', ['title' => 'Test Book']);
});
