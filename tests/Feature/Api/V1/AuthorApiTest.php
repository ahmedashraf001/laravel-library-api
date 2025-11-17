<?php

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Test 1: List Authors
test('can list all authors', function () {
    // Arrange: Create 3 authors
    Author::factory()->count(3)->create();

    // Act: Send GET request
    $response = $this->getJson('/api/v1/authors');

    // Assert: Check response
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'bio', 'dob', 'created_at', 'updated_at']
            ]
        ]);
});

// Test 2: Create Author with valid data
test('can create a new author with valid data', function () {
    // Arrange: Prepare author data
    $authorData = [
        'name' => 'Neil Gaiman',
        'bio' => 'English author of short fiction, novels, comic books, and films',
        'dob' => '1960-11-10'
    ];

    // Act: Send POST request
    $response = $this->postJson('/api/v1/authors', $authorData);

    // Assert: Check response and database
    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Neil Gaiman')
        ->assertJsonPath('data.dob', '1960-11-10');

    $this->assertDatabaseHas('authors', [
        'name' => 'Neil Gaiman',
        'dob' => '1960-11-10'
    ]);
});

// Test 3: Validation errors
test('returns validation errors when creating author with invalid data', function () {
    // Arrange: Invalid data (empty name, invalid date)
    $invalidData = [
        'name' => '',
        'dob' => 'invalid-date'
    ];

    // Act: Send POST request
    $response = $this->postJson('/api/v1/authors', $invalidData);

    // Assert: Check validation error response
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['The name field is required.'],
                'dob' => ['The dob must be a valid date.']
            ]
        ]);
});

// Test 4: Update Author
test('can update an existing author', function () {
    // Arrange: Create an author
    $author = Author::factory()->create([
        'name' => 'Original Name',
        'bio' => 'Original bio',
        'dob' => '1980-01-01'
    ]);

    // Act: Send PUT request to update
    $response = $this->putJson("/api/v1/authors/{$author->id}", [
        'name' => 'Updated Name',
        'bio' => 'Updated bio',
        'dob' => '1985-05-15'
    ]);

    // Assert: Check response and database
    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.bio', 'Updated bio')
        ->assertJsonPath('data.dob', '1985-05-15');

    $this->assertDatabaseHas('authors', [
        'id' => $author->id,
        'name' => 'Updated Name',
        'bio' => 'Updated bio'
    ]);
});
