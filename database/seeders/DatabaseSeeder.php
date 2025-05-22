<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Optional: Clean tables for development use only
        Schema::disableForeignKeyConstraints();
        DB::table('transactions')->truncate();
        DB::table('books')->truncate();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create admin user (avoids duplicate error)
        $admin = User::firstOrCreate(
            ['email' => 'admin@library.com'],
            [
                'name' => 'Library Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'remember_token' => Str::random(10),
            ]
        );

        // Create regular users
        $users = User::factory()->count(10)->create([
            'role' => 'user',
            'password' => Hash::make('password'), // Consistent password for testing
        ]);

        // Sample books
        $books = [
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'genre' => 'Fiction',
                'description' => 'A novel about the serious issues of rape and racial inequality.',
                'total_copies' => 5,
                'available_copies' => 5,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'genre' => 'Dystopian',
                'description' => 'A dystopian social science fiction novel and cautionary tale.',
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'genre' => 'Classic',
                'description' => 'A story of the fabulously wealthy Jay Gatsby and his love for Daisy Buchanan.',
                'total_copies' => 4,
                'available_copies' => 4,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'genre' => 'Classic',
                'description' => 'Romantic novel of manners.',
                'total_copies' => 6,
                'available_copies' => 6,
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'genre' => 'Fantasy',
                'description' => 'Fantasy novel about the adventures of Bilbo Baggins.',
                'total_copies' => 4,
                'available_copies' => 4,
            ]
        ];

        $createdBooks = collect();
        foreach ($books as $bookData) {
            $createdBooks->push(Book::create($bookData));
        }

        // Create sample transactions
        foreach ($users as $user) {
            $booksToBorrow = $createdBooks->random(rand(1, 3));

            foreach ($booksToBorrow as $book) {
                if ($book->available_copies > 0) {
                    $borrowedDate = Carbon::now()->subDays(rand(1, 30));
                    $isReturned = rand(0, 1);

                    Transaction::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'borrowed_date' => $borrowedDate,
                        'due_date' => $borrowedDate->copy()->addDays(14),
                        'status' => $isReturned ? 'returned' : 'borrowed',
                        'returned_date' => $isReturned ? $borrowedDate->copy()->addDays(rand(1, 14)) : null,
                    ]);

                    if ($book->available_copies > 0) {
                        $book->decrement('available_copies');
                    }
                }
            }
        }
    }
}
