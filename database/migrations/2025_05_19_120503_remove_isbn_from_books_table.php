<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIsbnFromBooksTable extends Migration
{
    public function up(): void
    {
        // Only drop if column exists
        if (Schema::hasColumn('books', 'isbn')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('isbn');
            });
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn')->nullable();
        });
    }
}
