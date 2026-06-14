<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Public ────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── Auth-protected (all MFC via Route::livewire) ──────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::livewire('/dashboard', 'dashboard')->name('dashboard');

    // Rooms
    Route::livewire('/rooms', 'rooms.room-list')->name('rooms.index');

    // Reservations
    Route::livewire('/reservations', 'reservations.reservation-list')->name('reservations.index');

    // Booking Calendar
    Route::livewire('/calendar', 'calendar')->name('calendar');

    // Guests
    Route::livewire('/guests', 'guests.guest-list')->name('guests.index');

    // Operations
    Route::livewire('/check-in',  'check-in')->name('checkin.index');
    Route::livewire('/check-out', 'check-out')->name('checkout.index');
    Route::livewire('/invoices',  'invoices.invoice-list')->name('invoices.index');
    Route::livewire('/housekeeping', 'housekeeping.housekeeping-list')->name('housekeeping.index');
    Route::livewire('/maintenance',  'maintenance.maintenance-list')->name('maintenance.index');

    // Reports
    Route::livewire('/reports/daily',     'reports.daily')->name('reports.daily');
    Route::livewire('/reports/occupancy', 'reports.occupancy')->name('reports.occupancy');
    Route::livewire('/reports/revenue',   'reports.revenue')->name('reports.revenue');

    // Admin
    Route::livewire('/users',    'users.user-list')->name('users.index');
    Route::livewire('/settings', 'settings')->name('settings');

    // Invoice PDF actions (controller still needed for DomPDF)
    Route::get('/invoice/download/{id}', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('/invoice/view/{id}',     [\App\Http\Controllers\InvoiceController::class, 'view'])->name('invoice.view');
});
