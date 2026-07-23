<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Customer Management
Route::resource('customers', CustomerController::class)->except(['create', 'edit']);
Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
Route::post('customers/{customer}/test-smtp', [CustomerController::class, 'testSmtp'])->name('customers.test-smtp');
Route::get('customers/sample-csv', [CustomerController::class, 'downloadSampleCsv'])->name('customers.sample-csv');
Route::post('customers/import-csv', [CustomerController::class, 'importCsv'])->name('customers.import-csv');

// Send Mail Module
Route::get('send-mail', [SendMailController::class, 'index'])->name('send-mail.index');
Route::get('send-mail/stock-details', [SendMailController::class, 'getStockDetails'])->name('send-mail.stock-details');
Route::post('send-mail/send', [SendMailController::class, 'send'])->name('send-mail.send');

// Email Templates Module (WYSIWYG Template Manager)
Route::resource('templates', EmailTemplateController::class)->except(['create', 'edit']);

// Email History Logs
Route::get('email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');
Route::get('email-logs-export', [EmailLogController::class, 'export'])->name('email-logs.export-csv');
Route::get('email-logs-export-alias', [EmailLogController::class, 'export'])->name('email-logs.export');
Route::get('email-logs/{log}', [EmailLogController::class, 'show'])->name('email-logs.show');
Route::post('email-logs/{log}/resend', [EmailLogController::class, 'resend'])->name('email-logs.resend');

// Settings & System Config
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

// Future Features Showcase
Route::get('future-features', function () {
    return view('future-features');
})->name('future-features');
