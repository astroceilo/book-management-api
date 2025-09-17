<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     */
    public function test_can_create_book(): void
    {
        // $response = $this->get('/');

        // $response->assertStatus(200);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/books', [
            'title' => 'Belajar Laravel',
            'author' => 'Odo',
            'published_year' => 2025,
            'isbn' => '1234567890123',
            'stock' => 5,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('books', ['title' => 'Belajar Laravel']);
    }

    public function test_can_update_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/books/{$book->id}", [
            'title' => 'Laravel Update',
            'author' => 'Updated Author',
            'published_year' => 2024,
            'isbn' => '9876543210123',
            'stock' => 3,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('books', ['title' => 'Laravel Update']);
    }

    public function test_can_delete_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200);
        // $response->assertStatus(204);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
