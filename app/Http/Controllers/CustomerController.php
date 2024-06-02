<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\UlasanBuku;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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
        return view('customer.dashboard', ['categories' => $categories, 'books' => $books, 'recommendedBooks' => $recommendedBooks]);
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

        // Mengambil rata-rata rating tiap buku
        $ratings = UlasanBuku::select('buku_id', DB::raw('AVG(rating) as average_rating'))
            ->groupBy('buku_id')
            ->get()
            ->keyBy('buku_id');

        // Menambahkan average_rating ke setiap book
        foreach ($books as $book) {
            $book->average_rating = $ratings->has($book->buku_id) ? $ratings[$book->buku_id]->average_rating : 0;
        }
        return view('customer.allbook', ['categories' => $categories, 'books' => $books]);
    }

    public function getBookDetails($id)
    {
        $book = Buku::with('ulasanBuku.user')->findOrFail($id);
        return view('partials.book_details', compact('book'));
    }
    

    public function profile()
    {
        $user = auth()->user();

        // Update statuses based on the return date
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

        return view('customer.profile', compact('peminjamanPending', 'peminjamanApproved', 'peminjamanRejected', 'peminjamanMustReturn', 'peminjamanReturned', 'user', 'ulasan'));
    }

    public function updateProfile(Request $request, $id)
    {
        // Validasi data yang diterima dari formulir
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Peroleh user yang sedang login
        $user = auth()->user();

        // Update gambar profil jika ada file yang diunggah
        if ($request->hasFile('profile_image')) {
            // Ambil file gambar yang diunggah
            $image = $request->file('profile_image');
            // Simpan file gambar ke dalam penyimpanan
            $imageName = $image->getClientOriginalName();
            $image->storeAs('public/profile_images', $imageName);
            // Simpan nama file gambar ke dalam kolom profile_image di tabel users
            $user->profile_image = $imageName;
        }

        if ($user instanceof User) {
            // Lakukan penyimpanan perubahan pada objek user
            $user->username = $request->username;
            $user->save();
            Alert::success('Success', 'Profile updated successfully');
        } else {
            Alert::alert('Failed', 'Profile updated fail');
        }

        return redirect()->route('profile')->with('success', 'Permintaan peminjaman berhasil diajukan.');
    }

    public function storePeminjaman(Request $request)
    {
        $userId = Auth::id();
        $bukuId = $request->input('buku_id');

        // Cek apakah pengguna sudah meminjam buku ini
        $existingPeminjaman = Peminjaman::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->where('status', '!=', 'buku sudah dikembalikan')
            ->first();

        if ($existingPeminjaman) {
            Alert::error('Error', 'Anda sudah meminjam buku ini.');
            return redirect()->back();
        }

        // Lanjutkan untuk menyimpan data peminjaman baru
        $peminjaman = new Peminjaman();
        $peminjaman->user_id = $userId;
        $peminjaman->buku_id = $bukuId;
        $peminjaman->tanggal_peminjaman = now(); // Set tanggal peminjaman
        // $peminjaman->tanggal_pengembalian = $request->input('tanggal_pengembalian');
        $peminjaman->status = 'pending';
        $peminjaman->save();

        Alert::success('Success', 'Terimakasih, Tunggu aproval dari petugas');
        return redirect()->route('allBook');
    }

    public function getUlasan()
    {
        $ulasan = UlasanBuku::with('user')->get(); // Include related user data
        return response()->json($ulasan);
    }
}
