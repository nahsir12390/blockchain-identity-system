<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\IdentityReviewController;
use App\Http\Controllers\Admin\LedgerIntegrityController;
use App\Http\Controllers\Admin\SmartContractController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\PublicVerificationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('verify', [PublicVerificationController::class, 'index'])->name('verify.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('identity', [IdentityController::class, 'index'])->name('identity.index');
    Route::get('identity/create', [IdentityController::class, 'create'])->name('identity.create');
    Route::post('identity', [IdentityController::class, 'store'])->name('identity.store');
    Route::get('identity/{identity}/document', [IdentityController::class, 'document'])->name('identity.document');
    Route::get('identity/{identity}/certificate', [IdentityController::class, 'certificate'])->name('identity.certificate');
    Route::patch('identity/{identity}/resubmit', [IdentityController::class, 'resubmit'])->name('identity.resubmit');

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('identities', [IdentityReviewController::class, 'index'])->name('identities.index');
        Route::get('identities/{identity}', [IdentityReviewController::class, 'show'])->name('identities.show');
        Route::patch('identities/{identity}/verify', [IdentityReviewController::class, 'verify'])->name('identities.verify');
        Route::patch('identities/{identity}/reject', [IdentityReviewController::class, 'reject'])->name('identities.reject');
        Route::patch('identities/{identity}/revoke', [IdentityReviewController::class, 'revoke'])->name('identities.revoke');
        Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('ledger-integrity', [LedgerIntegrityController::class, 'index'])->name('ledger.index');
        Route::get('smart-contract', [SmartContractController::class, 'index'])->name('smart-contract.index');
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/promote', [UserManagementController::class, 'promote'])->name('users.promote');
        Route::patch('users/{user}/demote', [UserManagementController::class, 'demote'])->name('users.demote');
    });
});

require __DIR__.'/settings.php';
