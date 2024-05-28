<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [AuthApiController::class, 'login']);
Route::post('register', [AuthApiController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthApiController::class, 'logout']);
    Route::get('customer', [CustomerApiController::class, 'customer']);
    Route::get('books', [CustomerApiController::class, 'getAllBook']);
    Route::get('profile', [CustomerApiController::class, 'profile']);
    Route::post('profile/update/{id}', [CustomerApiController::class, 'updateProfile']);
    Route::post('peminjaman', [CustomerApiController::class, 'storePeminjaman']);
    Route::get('ulasan', [CustomerApiController::class, 'getUlasan']);
    Route::post('ulasan', [UlasanController::class, 'store']);
    Route::get('ulasan', [UlasanController::class, 'getUlasan']);
});

// Route::get('user/all/', [LibraryController::class, 'getAll'])->name('getAllUser');
Route::get('/categories', [CategoryController::class, 'getAll'])->name('getAllCategory');
Route::get('/categories', [PetugasController::class, 'getAll'])->name('getAllCategory');
Route::get('/peminjaman', [AdminController::class, 'getPinjam'])->name('getAllPeminjaman');
Route::get('/peminjaman', [PetugasController::class, 'getPinjam'])->name('PeminjamanPetugas');
Route::get('/ulasan', [CustomerController::class, 'getUlasan'])->name('allUlasan');

Route::get('/users', [AdminController::class, 'getAll'])->name('getAllUser');
Route::get('/books', [BookController::class, 'getAll'])->name('getAllBook');
Route::delete('book/delete/{id}', [BookController::class, 'destroyData']);  
Route::patch('/books', [BookController::class, 'getAll'])->name('getAllBook');

Route::post('/peminjaman/{id}/approve', [PetugasController::class, 'approve'])->name('peminjaman.approve');
Route::post('/peminjaman/{id}/reject', [PetugasController::class, 'reject'])->name('peminjaman.reject');
Route::post('/peminjaman/return/{id}', [PetugasController::class, 'returnBook'])->name('peminjaman.return');

Route::post('/peminjaman/admin/{id}/approve', [AdminController::class, 'approve'])->name('admin.approve');
Route::post('/peminjaman/admin/{id}/reject', [AdminController::class, 'reject'])->name('admin.reject');
Route::post('/peminjaman/admin/return/{id}', [AdminController::class, 'returnBook'])->name('admin.return');
Route::get('/peminjaman/filter', [AdminController::class, 'filterPeminjaman'])->name('peminjaman.filter');

Route::get('/peminjaman/export-pdf', [PeminjamanController::class, 'exportPDF'])->name('peminjaman.exportPDF');
// Route::post('/categories/create', [CategoryController::class, 'store'])->name('category.store');
//update
Route::put('categories/update/{id}', [CategoryController::class, 'updateData'])->name('getEdit');
Route::middleware('auth:api')->group(function () {
    Route::post('admin/users', [AdminController::class, 'store']);
    Route::put('admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('admin/users/{id}', [AdminController::class, 'destroyData']);

});
Route::post('book-edit/{id}', [AdminController::class, 'edit']);
Route::put('/books/{id}', [BookController::class, 'update']);

Route::delete('categories/delete/{id}', [CategoryController::class, 'destroyData'])->name('student.delete');
Route::delete('petuga/book/delete/{id}', [PetugasController::class, 'destroyData'])->name('student.delete');