<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\UlasanBuku;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $categories = Kategori::all();

        $books = Buku::when($request->judul, function ($query, $judul) {
            return $query->where('judul', 'like', '%' . $judul . '%');
        })
            ->when($request->kategori, function ($query, $kategori) {
                return $query->whereHas('kategori', function ($q) use ($kategori) {
                    $q->where('kategori.kategori_id', $kategori);
                });
            })
            ->get();

        $ratings = UlasanBuku::select('buku_id', DB::raw('AVG(rating) as average_rating'))
            ->groupBy('buku_id')
            ->get()
            ->keyBy('buku_id');

        foreach ($books as $book) {
            $book->average_rating = $ratings->has($book->buku_id) ? $ratings[$book->buku_id]->average_rating : 0;
        }

        return response()->json(['categories' => $categories, 'books' => $books]);
    }

    public function profile()
    {
        $user = auth()->user();

        $today = now()->toDateString();
        Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'approved')
            ->where('tanggal_pengembalian', '<=', $today)
            ->update(['status' => 'buku harus dikembalikan']);

        $peminjamanPending = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'pending')
            ->with('buku')
            ->get();

        $peminjamanApproved = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'approved')
            ->with('buku')
            ->get();

        $peminjamanRejected = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'rejected')
            ->with('buku')
            ->get();

        $peminjamanReturned = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'buku sudah dikembalikan')
            ->with('buku')
            ->get();

        $peminjamanMustReturn = Peminjaman::where('user_id', $user->user_id)
            ->where('status', 'buku harus dikembalikan')
            ->with('buku')
            ->get();

        $ulasan = UlasanBuku::where('user_id', $user->user_id)->get();

        return response()->json(compact('peminjamanPending', 'peminjamanApproved', 'peminjamanRejected', 'peminjamanMustReturn', 'peminjamanReturned', 'user', 'ulasan'));
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
        $userId = Auth::id();
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
