<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\UlasanBuku;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerApiController extends Controller
{
    public function customer()
    {
        $categories = Kategori::all();
        $books = Buku::all();

        $recommendedBooks = Buku::with('kategori')
            ->withAvg('ulasanBuku', 'rating')
            ->orderByDesc('ulasan_buku_avg_rating')
            ->take(4)
            ->get();

        return response()->json(['categories' => $categories, 'books' => $books, 'recommendedBooks' => $recommendedBooks]);
    }

    public function getAllBook(Request $request)
    {
        // $categories = Kategori::all();

        // $books = Buku::when($request->judul, function ($query, $judul) {
        //     return $query->where('judul', 'like', '%' . $judul . '%');
        // })
        //     ->when($request->kategori, function ($query, $kategori) {
        //         return $query->whereHas('kategori', function ($q) use ($kategori) {
        //             $q->where('kategori.kategori_id', $kategori);
        //         });
        //     })
        //     ->with('kategori') // Load the relation
        //     ->get();

        // $ratings = UlasanBuku::select('buku_id', DB::raw('AVG(rating) as average_rating'))
        //     ->groupBy('buku_id')
        //     ->get()
        //     ->keyBy('buku_id');

        // foreach ($books as $book) {
        //     $book->average_rating = $ratings->has($book->buku_id) ? $ratings[$book->buku_id]->average_rating : 0;
        // }

        // $formattedBooks = $books->map(function ($book) {
        //     return [
        //         'buku_id' => $book->buku_id,
        //         'judul' => $book->judul,
        //         'penerbit' => $book->penerbit,
        //         'status' => $book->status,
        //         'stock' => $book->stock,
        //         'cover' => $book->cover,
        //         'kategori' => $book->kategori->map(function ($category) {
        //             return [
        //                 'kategori_id' => $category->kategori_id,
        //                 'nama_kategori' => $category->nama_kategori,
        //             ];
        //         }),
        //         'average_rating' => $book->average_rating,
        //     ];
        // });
        $books = Buku::with('kategori') // Load the relation
            ->get();

        $formattedBooks = $books->map(function ($book) {
            return [
                'buku_id' => $book->buku_id,
                'judul' => $book->judul,
                'penerbit' => $book->penerbit,
                'status' => $book->status,
                'stock' => $book->stock,
                'cover' => $book->cover,
                'kategori' => $book->kategori->map(function ($category) {
                    return [
                        'kategori_id' => $category->kategori_id,
                        'nama_kategori' => $category->nama_kategori,
                    ];
                }),
                'average_rating' => $book->average_rating ?? 0, // Default to 0 if not present
            ];
        });

        return response()->json(['books' => $formattedBooks]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            Log::error('User not authenticated or user ID not found in request');
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        Log::info('Profile endpoint hit', ['user_id' => $user->user_id]);

        // Update statuses based on the return date
        $today = Carbon::now()->toDateString();
        Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'approved')
            ->where('tanggal_pengembalian', '<=', $today)
            ->update(['status' => 'buku harus dikembalikan']);
        Log::info('Status updated for overdue books');

        $peminjamanPending = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'pending')
            ->with('buku')
            ->get();
        Log::info('Pending loans retrieved', ['peminjamanPending' => $peminjamanPending]);

        $peminjamanApproved = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'approved')
            ->with('buku')
            ->get();
        Log::info('Approved loans retrieved', ['peminjamanApproved' => $peminjamanApproved]);

        $peminjamanRejected = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'rejected')
            ->with('buku')
            ->get();
        Log::info('Rejected loans retrieved', ['peminjamanRejected' => $peminjamanRejected]);

        $peminjamanReturned = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'buku sudah dikembalikan')
            ->with('buku')
            ->get();
        Log::info('Returned loans retrieved', ['peminjamanReturned' => $peminjamanReturned]);

        $peminjamanMustReturn = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'buku harus dikembalikan')
            ->with('buku')
            ->get();
        Log::info('Must return loans retrieved', ['peminjamanMustReturn' => $peminjamanMustReturn]);

        $ulasan = UlasanBuku::where('user_id', $user->user_id)->get();
        Log::info('Reviews retrieved', ['ulasan' => $ulasan]);

        return response()->json([
            'user' => $user,
            'peminjamanPending' => $peminjamanPending,
            'peminjamanApproved' => $peminjamanApproved,
            'peminjamanRejected' => $peminjamanRejected,
            'peminjamanReturned' => $peminjamanReturned,
            'peminjamanMustReturn' => $peminjamanMustReturn,
            'ulasan' => $ulasan,
        ]);
    }


    public function updateProfile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = $image->getClientOriginalName();
            $image->storeAs('public/profile_images', $imageName);
            $user->profile_image = $imageName;
        }

        if ($user instanceof User) {
            $user->username = $request->username;
            $user->save();
            return response()->json(['message' => 'Profile updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Profile update failed'], 500);
        }
    }

    public function storePeminjaman(Request $request)
    {
        $userId = $request->input('user_id'); // Mengambil user_id dari inputan
        $bukuId = $request->input('buku_id');

        $existingPeminjaman = Peminjaman::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->where('status', '!=', 'buku sudah dikembalikan')
            ->first();

        if ($existingPeminjaman) {
            return response()->json(['message' => 'Anda sudah meminjam buku ini.'], 400);
        }

        $peminjaman = new Peminjaman();
        $peminjaman->user_id = $userId;
        $peminjaman->buku_id = $bukuId;
        $peminjaman->tanggal_peminjaman = now();
        $peminjaman->status = 'pending';
        $peminjaman->save();

        return response()->json(['message' => 'Terimakasih, Tunggu aproval dari petugas'], 201);
    }

    public function getUlasan()
    {
        $ulasan = UlasanBuku::with('user')->get();
        return response()->json($ulasan);
    }
}
