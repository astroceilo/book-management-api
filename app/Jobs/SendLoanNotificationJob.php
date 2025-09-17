<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendLoanNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $book;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw("Halo {$this->user->name}, kamu berhasil meminjam buku '{$this->book->title}'.", function ($message) {
            $message->to($this->user->email)
                    ->subject("Notifikasi Peminjaman Buku");
        });
    }
}
