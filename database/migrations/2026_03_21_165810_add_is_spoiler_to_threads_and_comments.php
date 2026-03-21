<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->boolean('is_spoiler')->default(false)->after('is_nsfw');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_spoiler')->default(false)->after('is_nsfw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('is_spoiler');
        });
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_spoiler');
        });
    }
};
