<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        // Filter / search
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('author')) {
            $query->where('author', $request->author);
        }
        if ($request->filled('year')) {
            $query->where('published_year', $request->year);
        }

        // return $query->paginate(10);
        // return response()->json($query->paginate(10), 200);
        return BookResource::collection($query->paginate(10), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        // return response()->json([
        //     'message' => 'Buku berhasil ditambahkan',
        //     'book' => $book,
        // ], 201);

        return (new BookResource($book))
            ->additional(['message'=>'Buku berhasil ditambahkan'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // return $book;
        // return response()->json($book, 200);
        return new BookResource($book, 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());
        // return response()->json([
        //     'message' => 'Data buku berhasil diubah',
        //     'book' => $book,
        // ], 201);

        return (new BookResource($book))
            ->additional(['message'=>'Data buku berhasil diperbarui'])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Data buku berhasil dihapus']);
        // return response()->noContent(); // 204 No Content
    }
}
