<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $bookCount = Buku::count();
    $categoryCount = Kategori::count();
    $userCount = User::count();
    $roles = Role::all();
    return view('admin.index_admin', [
        'book_count' => $bookCount,
        'category_count' => $categoryCount,
        'user_count' => $userCount,
        'roles' => $roles,
    ]);
    }

    public function getAll()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
         // Validasi input
    $validator = Validator::make($request->all(), [
        'username' => 'required|max:225',
        'email' => 'required|email|unique:user,email',
        'password' => 'required|max:225',
        'role_id' => 'required|exists:role,role_id',
        'status' => 'required|in:active,inactive',
    ]);

    // Jika validasi gagal, tampilkan SweetAlert
    if ($validator->fails()) {
        Alert::error('Error', 'Validation failed!');
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $hashedPassword = Hash::make($request->input('password'));

    // Buat user baru
    User::create([
        'username' => $request->username,
        'email' => $request->email,
        'password' => $hashedPassword,
        'role_id' => $request->role_id,
        'status' => $request->status,
    ]);

    Alert::success('Success', 'User added successfully!');
    return redirect()->route('admin.index_admin');
    }

    public function updateUser(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'username' => 'required|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            // Temukan pengguna yang akan diperbarui
            $user = User::findOrFail($id);

            // Perbarui username dan status
            $user->username = $request->username;
            $user->status = $request->status;

            // Simpan perubahan
            $user->save();
            Alert::success('Success', 'Users update successfully!');
            // Tampilkan pesan sukses dengan SweetAlert
            // Tampilkan pesan sukses berdasarkan status baru
            if ($request->status == 'active') {
                Alert::success('Success', 'User telah diaktifkan!');
            } else {
                Alert::success('Success', 'User telah dinonaktifkan!');
            }

            // Tampilkan pesan sukses
        } catch (Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyData($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            Alert::success('Success', 'User Behasil di hapus!');
            return $user;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function rent()
    {
        $today = Carbon::today();
        // Update status for overdue books
        Peminjaman::where('status', 'approved')
            ->where('tanggal_pengembalian', '<', $today)
            ->update(['status' => 'Buku harus dikembalikan']);

        // Get all relevant loans
        $peminjamanSemua = Peminjaman::whereIn('status', ['pending', 'approved', 'Buku harus dikembalikan'])->get();

        return view('admin.pinjam-admin', compact('peminjamanSemua'));
    }

    public function dataRent(Request $request)
    {
        $month = $request->input('month');
        // $year = $request->input('year');

        $query = Peminjaman::with('user', 'buku');

        if ($month) {
            $query->whereMonth('tanggal_peminjaman', $month);
            //   ->whereYear('tanggal_peminjaman', $year);
        }

        $peminjaman = $query->get();
        $users = User::all();

        if ($request->ajax()) {
            return view('partials.peminjaman-data', compact('peminjaman'))->render();
        }

        return view('admin.data-rent', compact('peminjaman', 'users'));
    }

    public function getPinjam()
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->get();
        return response()->json($peminjaman);
    }

    public function approve(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $tanggalPengembalian = Carbon::parse($request->input('tanggal_pengembalian'));
        $tanggalSekarang = Carbon::now();

        if ($peminjaman->status == 'pending') {
            $peminjaman->status = 'approved';
            $peminjaman->tanggal_pengembalian = $tanggalPengembalian;

            // Cek jika tanggal pengembalian sudah lewat
            if ($tanggalPengembalian->lessThan($tanggalSekarang)) {
                $peminjaman->status = 'buku harus dikembalikan';
            }

            $peminjaman->save();

            Alert::success('Success', 'Peminjaman buku telah disetujui.');
            return redirect()->back()->with('success', 'Peminjaman buku telah disetujui.');
        }

        return redirect()->back()->with('error', 'Peminjaman tidak valid.');
    }


    public function reject(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        if ($peminjaman->status != 'rejected') {
            $peminjaman->status = 'rejected';
            $peminjaman->save();
        }
        Alert::success('Success', 'Peminjaman berhasil direject');
        return redirect()->back()->with('success', 'Permintaan peminjaman berhasil ditolak.');
    }

    public function returnBook(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status != 'buku sudah dikembalikan') {
            $kondisiBuku = $request->input('kondisi_buku');
            $denda = 0;

            // Tambahkan logika untuk menangani kondisi 'Telat'
            if ($kondisiBuku === 'Telat') {
                $tanggalPengembalian = Carbon::parse($peminjaman->tanggal_pengembalian);
                $tanggalSekarang = Carbon::now();
                $hariKeterlambatan = $tanggalPengembalian->diffInDays($tanggalSekarang);

                // Hitung denda berdasarkan jumlah hari keterlambatan
                $denda = $hariKeterlambatan * 5000; // Misalnya, asumsi denda Rp 5000 per hari
            } elseif ($kondisiBuku === 'Rusak') {
                $denda += 30000;
            } elseif ($kondisiBuku === 'Hilang') {
                $denda += 100000;
            }

            $peminjaman->status = 'buku sudah dikembalikan';
            $peminjaman->kondisi_buku = $kondisiBuku;
            $peminjaman->denda = $denda;
            $peminjaman->save();

            $buku = Buku::findOrFail($peminjaman->buku_id);
            if ($buku->stock > 0) {
                $buku->status = 'In Stock';
            }
            $buku->save();
        }

        Alert::success('Success', 'Buku berhasil dikembalikan');
        return redirect()->back()->with('success', 'Buku berhasil dikembalikan.');
    }

    public function pinjam()
    {
        $peminjamanBelumDiproses = Peminjaman::all();
        return view('admin.pinjam-admin', compact('peminjamanBelumDiproses'));
    }


    public function destroyBuku($id)
    {
        try {
            $buku = Buku::findOrFail($id);
            $buku->delete();

            Alert::success('Success', 'Buku Behasil di hapus!');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showReviews($id)
    {
        $buku = Buku::with('ulasanBuku.user')->findOrFail($id);
        return view('admin.reviews', ['buku' => $buku]);
    }

    public function exportPDF(Request $request)
    {
        $month = $request->get('month');
        $query = Peminjaman::with(['user', 'buku']);

        if ($month) {
            $query->whereMonth('tanggal_peminjaman', $month);
        }

        $peminjamanSemua = $query->get();
        $selectedMonth = $month ? date('F', mktime(0, 0, 0, $month, 1)) : 'Semua Bulan';
        $pdf = PDF::loadView('admin.peminjaman-pdf', compact('peminjamanSemua', 'selectedMonth'));
        return $pdf->stream('data_peminjaman.pdf');
    }

    public function filterPeminjaman(Request $request)
    {
        $month = $request->input('month');

        $query = Peminjaman::query();

        if ($month) {
            $query->whereMonth('tanggal_peminjaman', $month);
        }

        $peminjaman = $query->with('user', 'buku')->get();

        return response()->json($peminjaman);
    }
}
