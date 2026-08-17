<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\Student\TicketController as StudentTicketController;
use Illuminate\Support\Facades\Route;

// ── Redirect root về login ──────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Auth Routes (Guest only) ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Microsoft SSO
    Route::get('/auth/microsoft/redirect', [MicrosoftController::class, 'redirect'])->name('auth.microsoft.redirect');
    Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback'])->name('auth.microsoft.callback');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Phân hệ Sinh viên / Giảng viên (REQUESTER) ────────────────────────
Route::middleware(['auth', 'role:REQUESTER,STAFF,MANAGER'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // UC03: Danh sách Ticket cá nhân
        Route::get('/tickets', [StudentTicketController::class, 'index'])
            ->name('tickets.index');

        // UC02: Tạo Ticket mới
        Route::get('/tickets/create', [StudentTicketController::class, 'create'])
            ->name('tickets.create');
        Route::post('/tickets', [StudentTicketController::class, 'store'])
            ->name('tickets.store');

        // UC04: Chi tiết Ticket
        Route::get('/tickets/{ticket}', [StudentTicketController::class, 'show'])
            ->name('tickets.show');

        // UC04: Gửi bình luận / chat
        Route::post('/tickets/{ticket}/comments', [StudentTicketController::class, 'addComment'])
            ->name('tickets.comments.store');

        // UC05: Mở lại Ticket
        Route::post('/tickets/{ticket}/reopen', [StudentTicketController::class, 'reopen'])
            ->name('tickets.reopen');

        // UC05: Gửi đánh giá 5 sao
        Route::post('/tickets/{ticket}/survey', [StudentTicketController::class, 'survey'])
            ->name('tickets.survey');
    });
