<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ExpenseAllocationController;
use App\Http\Controllers\GoalAllocationController;
use App\Http\Controllers\IncomeAllocationController;
use App\Http\Controllers\IncomeTransactionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('login');

Route::get('/auth/redirect', function () {
    $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
    dd($redirectUrl); // untuk lihat apakah redirect_uri-nya dikirim
});

Route::middleware('auth')->group(function () {
    // Resource route
    Route::resource('saving-goals', SavingGoalController::class);
    
    // Route tambahan khusus
    Route::post('/saving-goals/{id}/add-saved', [SavingGoalController::class, 'addSaved'])->name('saving-goals.add-saved');
    Route::patch('saving-goals/{goal}/archive', [SavingGoalController::class, 'archive'])->name('saving-goals.archive');
    Route::get('archived-goals', [SavingGoalController::class, 'archived'])->name('saving-goals.archived');
});

Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('income-allocations', IncomeAllocationController::class);

    Route::post('/goal-allocations', [GoalAllocationController::class, 'store'])->name('goal-allocations.store');

    Route::post('/expenses-allocations', [ExpenseAllocationController::class, 'store'])->name('expenses.allocations.store');
    Route::get('/expenses-allocations/get-existing', [ExpenseAllocationController::class, 'getExistingAllocations'])->name('expenses.allocations.getExisting');

    Route::resource('transactions', TransactionController::class);

});

// Google Auth Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Logout Route
Route::post('logout', [GoogleController::class, 'logout'])->name('logout');

