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
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminBoardController;
use App\Http\Controllers\Admin\AdminBanAppealController;
use App\Http\Controllers\BanAppealController;
use App\Models\Board;
use App\Http\Controllers\PopularController;
use App\Http\Controllers\PollController;

// Banned page (standalone, no auth required)
Route::get('/banned', fn () => view('admin.banned'))->name('banned.show');

// Ban appeal submission (guest, since user is logged out)
Route::post('/ban-appeal', [BanAppealController::class, 'store'])->name('ban-appeal.store');

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

    // Polls (vote)
    Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');
});

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
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

// Admin panel routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Boards CRUD
    Route::get('/boards', [AdminBoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [AdminBoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [AdminBoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/{board}/edit', [AdminBoardController::class, 'edit'])->name('boards.edit');
    Route::put('/boards/{board}', [AdminBoardController::class, 'update'])->name('boards.update');
    Route::delete('/boards/{board}', [AdminBoardController::class, 'destroy'])->name('boards.destroy');

    // Board moderators (existing)
    Route::get('/b/{board:slug}/moderators', [BoardModeratorController::class, 'index'])->name('board.moderators.index');
    Route::post('/b/{board:slug}/moderators', [BoardModeratorController::class, 'store'])->name('board.moderators.store');
    Route::delete('/b/{board:slug}/moderators/{user}', [BoardModeratorController::class, 'destroy'])->name('board.moderators.destroy');

    // Ban Appeals
    Route::get('/appeals', [AdminBanAppealController::class, 'index'])->name('appeals.index');
    Route::get('/appeals/{appeal}', [AdminBanAppealController::class, 'show'])->name('appeals.show');
    Route::patch('/appeals/{appeal}/approve', [AdminBanAppealController::class, 'approve'])->name('appeals.approve');
    Route::patch('/appeals/{appeal}/reject', [AdminBanAppealController::class, 'reject'])->name('appeals.reject');
});

require __DIR__.'/auth.php';

Route::fallback(function () {
    abort(404);
});

