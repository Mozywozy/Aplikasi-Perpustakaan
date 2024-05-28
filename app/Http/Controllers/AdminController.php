<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $bookCount = Buku::count();
        $categoryCount = Kategori::count();
        $userCount = User::count();
        return view('admin.index_admin', ['book_count' => $bookCount, 'category_count' => $categoryCount, 'user_count' => $userCount]);
    }

    public function getAll()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:225',
            'status' => 'required|in:active,inactive',
        ]);

        // Jika validasi gagal, tampilkan SweetAlert
        if ($validator->fails()) {
            Alert::error('Error', 'already exists!');
            return redirect('admin.index_admin');
        }

        $caregories = User::create($request->all());
        Alert::success('Success', 'Users added successfully!');

        return redirect('admin.index_admin');
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
        if ($peminjaman->status != 'approved') {
            $peminjaman->status = 'approved';
            $peminjaman->tanggal_pengembalian = $request->input('tanggal_pengembalian'); // Simpan tanggal pengembalian
            $peminjaman->save();

            $buku = Buku::findOrFail($peminjaman->buku_id);
            $buku->stock -= 1;
            // Periksa apakah stok habis
            if ($buku->stock == 0) {
                $buku->status = 'Out Stock';
            }
            $buku->save();
            // Stok buku akan berkurang otomatis oleh trigger di database
        }
        Alert::success('Success', 'Peminjaman diapprove');
        return redirect()->back()->with('success', 'Permintaan peminjaman berhasil disetujui.');
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

            if ($kondisiBuku === 'Rusak') {
                $denda = 30000;
            } elseif ($kondisiBuku === 'Hilang') {
                $denda = 100000;
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
