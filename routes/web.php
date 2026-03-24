<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\BoardModeratorController;
use App\Http\Controllers\ModeratorInvitationController;
use App\Models\Board;
use App\Http\Controllers\PopularController;



// Vote route (before fallback)
Route::middleware('anon')->post('/vote', [VoteController::class, 'store'])->name('vote.store');

Route::middleware('anon')->group(function () {
    // Home
    Route::get('/', function () {
        return view('home', ['boards' => Board::all()]);
    })->name('home');

    // Global Search
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    // Boards & Threads
    Route::get('/b/{board:slug}', [ThreadController::class, 'index'])->name('boards.show');
    Route::post('/b/{board:slug}/threads', [ThreadController::class, 'store'])->name('threads.store');

    // Thread detail
    Route::get('/t/{thread}', [ThreadController::class, 'show'])->name('threads.show');

    // Comments
    Route::post('/t/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Update (edit) & Delete (thread + comment)
    Route::patch('/t/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('/t/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');

    Route::patch('/c/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/c/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Popular
    Route::get('/popular', [PopularController::class, 'index'])->name('popular.index');
});

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');

    // Bookmarks
    Route::post('/t/{thread}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

    // User search (for @mention autocomplete)
    Route::get('/api/users/search', [UserSearchController::class, 'search'])->name('api.users.search');

    // Moderator invitation accept/decline
    Route::post('/moderator-invitation/{invitation}/accept', [ModeratorInvitationController::class, 'accept'])->name('moderator.invitation.accept');
    Route::post('/moderator-invitation/{invitation}/decline', [ModeratorInvitationController::class, 'decline'])->name('moderator.invitation.decline');
});

// Admin: Board moderator management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/b/{board:slug}/moderators', [BoardModeratorController::class, 'index'])->name('admin.board.moderators.index');
    Route::post('/admin/b/{board:slug}/moderators', [BoardModeratorController::class, 'store'])->name('admin.board.moderators.store');
    Route::delete('/admin/b/{board:slug}/moderators/{user}', [BoardModeratorController::class, 'destroy'])->name('admin.board.moderators.destroy');
});

require __DIR__.'/auth.php';

Route::fallback(function () {
    abort(404);
});

