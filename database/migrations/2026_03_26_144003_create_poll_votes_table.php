<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('anon_key', 100)->nullable()->index();
            $table->timestamps();

            // One vote per authenticated user per poll
            $table->unique(['poll_id', 'user_id']);
            // One vote per anon session per poll
            $table->unique(['poll_id', 'anon_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
