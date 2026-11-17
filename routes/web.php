<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController; // baris baru: memanggil MemberController
use App\Http\Controllers\ProfileController; // 1. Memanggil ProfileController

Route::get('/', [HomeController::class, 'index']);
Route::get('/tentang', [HomeController::class, 'tentang']);
Route::get('/kontak', [HomeController::class, 'kontak']);

// Seluruh route di bawah ini hanya bisa diakses setelah login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');

    // 2. Rute Profile (diperlukan oleh AuthenticatedLayout.vue)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. Rute Resource untuk Buku
    Route::resource('books', BookController::class);
    Route::middleware(['auth'])->group(function () {
    Route::resource('books', BookController::class);
    Route::resource('members', MemberController::class); // baris baru: 7 alamat CRUD untuk anggota
});
});

require __DIR__.'/auth.php';