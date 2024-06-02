<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\BukuCategory;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class PetugasController extends Controller
{
    public function index()
    {
        $bukus = Buku::all();
        $bookCount = Buku::count();
        $categories = Kategori::all();
        return view('petugas.petugas', ['bukus' => $bukus, 'book_count' => $bookCount, 'categories' => $categories]);
    }

    public function getPinjam()
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->get();
        return response()->json($peminjaman);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|unique:kategori|max:50'
        ]);
        // Jika validasi gagal, tampilkan SweetAlert
        if ($validator->fails()) {
            Alert::error('Error', 'Category already exists!');
            return redirect('petugas.category');
        }

        $caregories = Kategori::create($request->all());
        Alert::success('Success', 'Category added successfully!');

        return redirect('petugas.category');
    }

    public function updateData(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori,' . $id . ',kategori_id|max:50'
        ]);
    
        try {
            // Temukan kategori yang akan diperbarui
            $category = Kategori::findOrFail($id);
    
            // Perbarui nama kategori
            $category->nama_kategori = $request->nama_kategori;
    
            // Simpan perubahan
            $category->save();
    
            Alert::success('Success', 'Category Behasil di Edit!');
    
            // Berikan respons
            return $category;
        } catch (\Exception $e) {
            // Tangani kesalahan
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyData($id)
    {
        try {
            $category = Kategori::findOrFail($id);
            $category->delete();
            
            Alert::success('Success', 'Category Behasil di hapus!');
            return $category;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function kategori()
    {
        $categoryCount = Kategori::count();
        $categories = Kategori::all();
        return view('petugas.kategori', ['categories' => $categories, 'category_count' => $categoryCount]);
    }

    public function getAll()
    {
        $categories = Kategori::all();
        return response()->json($categories);
    }

    public function addBook()
    {
        $categories = Kategori::all();
        return view('petugas.add-book-petugas', compact('categories'));
    }

    public function storeBook(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'judul' => 'required|max:100',
            'penerbit' => 'required|max:100',
            'status' => 'required|in:In Stock,Out Stock',
            'stock' => 'required|integer|min:0',
            'kategori_id' => 'required|array', // Pastikan kategori_id adalah array
            'kategori_id.*' => 'exists:kategori,kategori_id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newName = 'cover_' . time() . '.' . $image->getClientOriginalExtension(); // Generate a unique file name
            $image->storeAs('covers', $newName); // Adjust the storage path as needed
        } else {
            $newName = ''; // Default cover image name if no image is uploaded
        }

        // Buat buku baru dengan data yang diterima
        $book = new Buku();
        $book->judul = $request->judul;
        $book->penerbit = $request->penerbit;
        $book->status = $request->status;
        $book->stock = $request->stock;
        $book->cover = $newName;
        // Tentukan status berdasarkan stok
        if ($request->stock > 0) {
            $book->status = 'In Stock';
        } else {
            $book->status = 'Out Stock';
        }
        $book->save();


        // Jika validasi gagal, tampilkan SweetAlert
        if ($validator->fails()) {
            Alert::error('Error', 'already exists!');
            return redirect('admin.books');
        }

        foreach ($request->kategori_id as $kategoriId) {
            BukuCategory::create([
                'buku_id' => $book->id,
                'kategori_id' => $kategoriId
            ]);
        }

        $book->kategori()->attach($request->kategori_id);

        $request['cover'] = $newName;
        $book->kategori()->sync($request->kategori_id);
        Alert::success('Success', 'Book added successfully!');

        return redirect('petugas');
    }

    public function edit($id)
    {
        $book = Buku::findOrFail($id);
        return view('petugas.petugas', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari formulir
        $validator = Validator::make($request->all(), [
            'judul' => 'required|max:100',
            'penerbit' => 'required|max:100',
            'status' => 'required|in:In Stock,Out Stock',
            'stock' => 'required|integer|min:0',
            'kategori_id' => 'required|array',
            'kategori_id.*' => 'exists:kategori,kategori_id',
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Ambil buku berdasarkan ID
        $book = Buku::findOrFail($id);

        // Perbarui atribut-atribut buku sesuai data yang diterima
        $book->judul = $request->judul;
        $book->penerbit = $request->penerbit;
        $book->status = $request->status;
        $book->stock = $request->stock;

        // Tentukan status berdasarkan stok
        if ($request->stock > 0) {
            $book->status = 'In Stock';
        } else {
            $book->status = 'Out Stock';
        }

        // Jika terdapat file gambar yang diunggah, proses penyimpanannya
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newName = 'cover_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('cover', $newName);
            $book->cover = $newName;
        }

        // Simpan perubahan buku
        $book->save();
        Alert::success('Success', 'Buku Behasil di Edit!');

        // Proses kategori buku
        $book->kategori()->sync($request->kategori_id);

        // Jika berhasil, kembalikan respons sukses
        return response()->json(['message' => 'Book updated successfully'], 200);
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


    public function rent()
    {
        $peminjamanBelumDiproses = Peminjaman::all();
        return view('petugas.pinjam', compact('peminjamanBelumDiproses'));
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

    // PetugasController.php
    public function showReviews($id)
    {
        $buku = Buku::with('ulasanBuku.user')->findOrFail($id);
        return view('petugas.reviews', ['buku' => $buku]);
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
