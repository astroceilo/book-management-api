<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\LoanResource;
use App\Jobs\SendLoanNotificationJob;
use App\Jobs\SendReturnNotificationJob;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $books = $user->books()->get();

        // return response()->json($books, 200);
        return LoanResource::collection($books)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function borrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);
        $user = $request->user();

        // Cek stok
        if ($book->stock < 1) {
            return response()->json(['message' => 'Stok buku habis'], 400);
        }

        // Cek apakah user sudah meminjam buku ini
        if ($user->books()->wherePivot('book_id', $book->id)
            ->wherePivot('returned_at', null)
            ->exists()) 
        {
            return response()->json(['message'=>'Buku ini sedang Anda pinjam'], 400);
        }

        DB::transaction(function() use ($user, $book) {
            // Kurangi stok buku
            $book->decrement('stock', 1);

            // Tambah record pivot
            $user->books()->attach($book->id, [
                'loaned_at' => now()
            ]);
        });

        // Notif email
        SendLoanNotificationJob::dispatch($user, $book);

        // return response()->json([
        //     'message' => 'Buku berhasil dipinjam',
        //     'book' => $book
        // ], 201);

        // Ambil data pinjaman terbaru
        $pivotBook = $user->books()
            ->where('books.id', $book->id)
            ->orderBy('book_loans.id', 'desc')
            ->first();

        return (new LoanResource($pivotBook))
            ->additional(['message' => 'Buku berhasil dipinjam'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function return(Request $request)
    {
        $request->validate([
            'book_id'=>'required|exists:books,id'
        ]);

        $book = Book::findOrFail($request->book_id);
        $user = $request->user();

        // Cari pinjaman aktif (belum dikembalikan)
        $pivot = $user->books()
            ->where('books.id', $book->id)
            ->wherePivot('returned_at', null)
            ->orderBy('book_loans.id', 'desc')
            ->first();

        if(!$pivot){
            return response()->json(['message'=>'Tidak ada pinjaman aktif untuk buku ini'],400);
        }

        DB::transaction(function() use ($user, $pivot, $book){
            // Tambah stok buku
            $book->increment('stock',1);

            // Tambah record pivot
            // $user->books()->updateExistingPivot($book->id, [
            //     'returned_at'=> now ()
            // ]);

            // update langsung baris pivot yang ditemukan
            $pivot->pivot->update([
                'returned_at' => now()
            ]);
        });

        // Notif email
        SendReturnNotificationJob::dispatch($user, $book);

        // return response()->json([
        //     'message'=>'Buku berhasil dikembalikan',
        //     'book'=> $book
        // ], 200);

        // return (new LoanResource($book))
        //     ->additional(['message'=>'Buku berhasil dikembalikan'])
        //     ->response()
        //     ->setStatusCode(200);

        // Ambil data pinjaman terakhir (supaya ada returned_at)
        $pivotBook = $user->books()
            ->where('books.id', $book->id)
            ->orderBy('book_loans.id', 'desc')
            ->first();

        return (new LoanResource($pivotBook))
            ->additional(['message' => 'Buku berhasil dikembalikan'])
            ->response()
            ->setStatusCode(200);
    }
}
