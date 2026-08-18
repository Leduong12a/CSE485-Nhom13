<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\Manager\CategoryController as ManagerCategoryController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\SlaReportController as ManagerSlaReportController;
use App\Http\Controllers\Manager\TicketController as ManagerTicketController;
use App\Http\Controllers\Staff\ProfileController as StaffProfileController;
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\Staff\WorkdeskController as StaffWorkdeskController;
use App\Http\Controllers\Student\TicketController as StudentTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/auth/microsoft/redirect', [MicrosoftController::class, 'redirect'])->name('auth.microsoft.redirect');
    Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback'])->name('auth.microsoft.callback');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:REQUESTER,STAFF,MANAGER'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/tickets', [StudentTicketController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/create', [StudentTicketController::class, 'create'])
            ->name('tickets.create');
        Route::post('/tickets', [StudentTicketController::class, 'store'])
            ->name('tickets.store');

        Route::get('/tickets/{ticket}', [StudentTicketController::class, 'show'])
            ->name('tickets.show');

        Route::post('/tickets/{ticket}/comments', [StudentTicketController::class, 'addComment'])
            ->name('tickets.comments.store');

        Route::post('/tickets/{ticket}/reopen', [StudentTicketController::class, 'reopen'])
            ->name('tickets.reopen');

        Route::post('/tickets/{ticket}/survey', [StudentTicketController::class, 'survey'])
            ->name('tickets.survey');

        Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])
            ->name('profile.index');
        Route::post('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])
            ->name('profile.update');
        Route::post('/profile/password', [\App\Http\Controllers\Student\ProfileController::class, 'changePassword'])
            ->name('profile.password');
    });

Route::middleware(['auth', 'role:STAFF,MANAGER'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/workdesk', [StaffWorkdeskController::class, 'index'])
            ->name('workdesk.index');

        Route::get('/workdesk/kanban', [StaffWorkdeskController::class, 'kanban'])
            ->name('workdesk.kanban');

        Route::get('/tickets/{ticket}', [StaffTicketController::class, 'show'])
            ->name('tickets.show');
        Route::post('/tickets/{ticket}/claim', [StaffTicketController::class, 'claim'])
            ->name('tickets.claim');
        Route::post('/tickets/{ticket}/reassign', [StaffTicketController::class, 'reassign'])
            ->name('tickets.reassign');
        Route::post('/tickets/{ticket}/status', [StaffTicketController::class, 'updateStatus'])
            ->name('tickets.status');
        Route::post('/tickets/{ticket}/comments', [StaffTicketController::class, 'addComment'])
            ->name('tickets.comments.store');
        Route::post('/tickets/{ticket}/release', [StaffTicketController::class, 'release'])
            ->name('tickets.release');

        Route::get('/profile', [StaffProfileController::class, 'index'])
            ->name('profile.index');
        Route::post('/profile', [StaffProfileController::class, 'update'])
            ->name('profile.update');
    });

Route::middleware(['auth', 'role:MANAGER'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [\App\Http\Controllers\Manager\ProfileController::class, 'index'])
            ->name('profile.index');
        Route::post('/profile', [\App\Http\Controllers\Manager\ProfileController::class, 'update'])
            ->name('profile.update');

        Route::get('/tickets', [ManagerTicketController::class, 'index'])
            ->name('tickets.index');
        Route::get('/tickets/{ticket}', [ManagerTicketController::class, 'show'])
            ->name('tickets.show');
        Route::post('/tickets/{ticket}/assign', [ManagerTicketController::class, 'assign'])
            ->name('tickets.assign');

        Route::get('/categories', [ManagerCategoryController::class, 'index'])
            ->name('categories.index');
        Route::post('/categories', [ManagerCategoryController::class, 'store'])
            ->name('categories.store');
        Route::put('/categories/{category}', [ManagerCategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/categories/{category}', [ManagerCategoryController::class, 'destroy'])
            ->name('categories.destroy');

        Route::get('/reports/sla', [ManagerSlaReportController::class, 'index'])
            ->name('reports.sla');
    });
