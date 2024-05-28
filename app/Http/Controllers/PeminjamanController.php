<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        // Update status for overdue books
        Peminjaman::where('status', 'approved')
            ->where('tanggal_pengembalian', '<', $today)
            ->update(['status' => 'Buku harus dikembalikan']);

        // Get all relevant loans
        $peminjamanSemua = Peminjaman::whereIn('status', ['pending', 'approved', 'Buku harus dikembalikan'])->get();

        return view('petugas.pinjam', compact('peminjamanSemua'));
    }

    public function store(Request $request)
    {
        // Validasi data yang dikirim
        $request->validate([
            'buku_id' => 'required|exists:buku,buku_id',
            // 'tanggal_pengembalian' => 'required|date|after_or_equal:today',
        ]);

        // Simpan data peminjaman ke dalam database
        Peminjaman::create([
            'buku_id' => $request->buku_id,
            'user_id' => auth()->id(),
            // 'tanggal_peminjaman' => now(),
            // 'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'status' => 'pending', // Menunggu persetujuan petugas
        ]);


        // $peminjaman = new Peminjaman();
        // $peminjaman->user_id = auth()->id(); // atau gunakan cara lain untuk mendapatkan ID pengguna
        // $peminjaman->buku_id = $request->buku_id;
        // $peminjaman->tanggal_peminjaman = now();
        // $peminjaman->tanggal_pengembalian = $request->tanggal_pengembalian;
        // $peminjaman->status = 'pending'; // atau status lainnya sesuai kebutuhan
        // $peminjaman->save();

        // Tampilkan pesan sukses dan kembalikan pengguna ke halaman sebelumnya
        // Log::info('Peminjaman created: ' . $request->buku_id . ' by user: ' . auth()->id());
        return redirect()->back()->with('success', 'Permintaan peminjaman berhasil diajukan.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'tanggal_pengembalian' => 'required|date',
        ]);

        // Temukan data peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'approved',
            'tanggal_pengembalian' => $request->tanggal_pengembalian
        ]);

        // Set status peminjaman menjadi "approved"
        // $peminjaman->status = 'approved';
        // $peminjaman->tanggal_pengembalian = $request->tanggal_pengembalian;
        // $peminjaman->save();

        // Tampilkan pesan sukses dan kembalikan pengguna ke halaman sebelumnya
        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'rejected';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Permintaan peminjaman berhasil ditolak.');
    }

    public function returnBook(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'Buku sudah dikembalikan!';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Buku berhasil dikembalikan.');
    }

    public function dataRent()
    {
        $peminjaman = Peminjaman::with('user')->get();
        $users = User::all();
        return view('petugas.data-peminjaman', compact('peminjaman', 'users'));
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
        $pdf = PDF::loadView('petugas.data-rent-pdf', compact('peminjamanSemua', 'selectedMonth'));
        return $pdf->stream('data_peminjaman.pdf');
    }
}
