<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UlasanController;
use Illuminate\Routing\RouteGroup;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->middleware('auth');

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('only_guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticating']);
    Route::get('register', [AuthController::class, 'register']);
    Route::post('register', [AuthController::class, 'registerProses']);
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('confirm-logout', [AuthController::class, 'confirmLogout'])->name('confirm.logout');

    Route::middleware(['only_admin'])->group(function () {
        Route::get('admin.index_admin', [AdminController::class, 'index'])->name('admin.index_admin');
        Route::get('admin.books', [BookController::class, 'index'])->name('admin.books');
        Route::get('admin.category', [CategoryController::class, 'index'])->name('admin.category');
        Route::post('admin.category', [CategoryController::class, 'store']);
        Route::put('admin.category/{id}', [CategoryController::class, 'updateData']);
        Route::delete('kategori/delete/{id}', [CategoryController::class, 'destroyData'])->name('categories.delete');

        Route::put('admin.index_admin/{id}', [AdminController::class, 'updateUser']);
        Route::post('admin.index_admin', [AdminController::class, 'store']);
        Route::delete('admin/delete/{id}', [AdminController::class, 'destroyData'])->name('user.delete');

        Route::get('books', [BookController::class, 'getAll']);
        Route::get('book-add', [BookController::class, 'add']);
        Route::post('book-add', [BookController::class, 'store']);
        Route::get('book-edit/{id}', [BookController::class, 'edit']);
        Route::put('book-edit/{id}', [BookController::class, 'update'])->name('book.update');
        Route::delete('book/delete/{id}', [BookController::class, 'destroyData'])->name('book.delete');
        Route::get('peminjaman', [AdminController::class, 'rent'])->name('admin.peminjaman');
        Route::get('data-pinjam', [AdminController::class, 'dataRent'])->name('admin.data-rent');
        Route::get('book/admin/{id}/reviews', [AdminController::class, 'showReviews'])->name('admin.reviews');
        Route::post('/peminjaman/admin/{id}/approve', [AdminController::class, 'approve'])->name('admin.approve');
        Route::post('/peminjaman/admin/{id}/reject', [AdminController::class, 'reject'])->name('admin.reject');
        Route::post('/peminjaman/admin/return/{id}', [AdminController::class, 'returnBook'])->name('admin.return');
        
        Route::get('/peminjaman/filter', [AdminController::class, 'filterPeminjaman'])->name('peminjaman.filter');

        Route::get('/peminjaman/export-pdf', [PeminjamanController::class, 'exportPDF'])->name('peminjaman.exportPDF');
    });

    Route::middleware(['only_petugas'])->group(function () {
        Route::get('petugas', [PetugasController::class, 'index'])->name('petugas.petugas');
        Route::get('petugas.category', [PetugasController::class, 'kategori'])->name('petugas.category');
        Route::get('petugas-add-book', [PetugasController::class, 'addBook']);
        Route::post('petugas-add-book', [PetugasController::class, 'storeBook']);
        Route::post('petugas.category', [PetugasController::class, 'store']);
        Route::put('petugas.category/{id}', [PetugasController::class, 'updateData']);
        Route::delete('categories/delete/{id}', [PetugasController::class, 'destroyData'])->name('categories.delete');
        Route::get('buku-petugas/{id}', [PetugasController::class, 'edit']);
        Route::put('buku-petugas/{id}', [PetugasController::class, 'update']);
        Route::delete('petugas/book/delete/{id}', [PetugasController::class, 'destroyBuku'])->name('buku.delete');
        Route::get('peminjaman-petugas', [PeminjamanController::class, 'index'])->name('petugas.peminjaman');
        Route::get('data-peminjaman', [PeminjamanController::class, 'dataRent'])->name('data-rent');
        // 
        Route::get('book/{id}/reviews', [PetugasController::class, 'showReviews'])->name('book.reviews');

        // Rute untuk menyetujui atau menolak permintaan peminjaman oleh petugas
        Route::post('/peminjaman/{id}/approve', [PetugasController::class, 'approve'])->name('peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [PetugasController::class, 'reject'])->name('peminjaman.reject');
        Route::post('/peminjaman/return/{id}', [PetugasController::class, 'returnBook'])->name('peminjaman.return');

        Route::get('/peminjaman/filter', [PetugasController::class, 'filterPeminjaman'])->name('petugas.filter');

        Route::get('/peminjaman/export-pdf', [PeminjamanController::class, 'exportPDF'])->name('petugas.exportPDF');
    });

    // Rute untuk menampilkan halaman buku dan mengajukan peminjaman
    Route::get('/books', [PeminjamanController::class, 'index'])->name('books.index');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');

    // ROUTE CUSTOMER   
    Route::middleware(['only_customer'])->group(function () {
        Route::get('customer', [CustomerController::class, 'customer'])->name('dashboard');
        Route::get('allBook', [CustomerController::class, 'getAllBook'])->name('allBook');
        Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
        Route::put('profile/{id}', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::post('/peminjaman/store', [CustomerController::class, 'storePeminjaman'])->name('peminjaman.store');
        Route::post('/ulasan/store', [UlasanController::class, 'store'])->name('ulasan.store');
    });
});
