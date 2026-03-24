<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to include 'user' value and set new default
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','moderator','user') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','moderator') NOT NULL DEFAULT 'moderator'");
    }
};
