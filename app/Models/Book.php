<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'published_year',
        'isbn',
        'stock',
    ];

    // Relasi ke User lewat pivot
    public function users()
    {
        return $this->belongsToMany(User::class, 'book_loans')
            ->using(BookLoan::class) // Model pivot
            ->withPivot('id', 'loaned_at', 'returned_at')
            ->withTimestamps();
    }
}
