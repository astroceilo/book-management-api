<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_borrow_book(): void
    {
        // $response = $this->get('/');

        // $response->assertStatus(200);

        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        $response = $this->actingAs($user)->postJson('/api/loans/borrow', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('book_loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'returned_at' => null
        ]);
    }

    public function test_user_cannot_borrow_if_stock_empty(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 0]);

        $response = $this->actingAs($user)->postJson('/api/loans/borrow', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(400);
    }

    public function test_user_can_return_book(): void
{
    $user = User::factory()->create();
    $book = Book::factory()->create(['stock' => 1]);

    $user->books()->attach($book->id, ['loaned_at' => now()]);

    $response = $this->actingAs($user)->postJson('/api/loans/return', [
        'book_id' => $book->id,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('book_loans', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'returned_at' => now()
    ]);

    $this->assertEquals(2, $book->fresh()->stock);
}
}
