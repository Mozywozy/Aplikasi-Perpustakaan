<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\BukuCategory;
use App\Models\Kategori;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        // $request->session()->flush();
        $bukus = Buku::with('kategori')->get();
        $categories = Kategori::all();
        return view('admin.books', compact('bukus', 'categories'));
    }

    public function getAll()
    {
        // return Kategori::all();
        $book = Buku::with('kategori')->get();
        // Transform the books collection to include kategori_names
        $book = $book->map(function ($books) {
            return array_merge($books->toArray(), ['nama_kategori' => $books->kategori_names]);
        });
        return response()->json($book);
    }

    public function add()
    {
        $categories = Kategori::all();
        return view('admin.book-add', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'judul' => 'required|max:100',
            'penerbit' => 'required|max:100',
            'sinopsis' => 'nullable|string',
            'status' => 'required|in:In Stock,Out Stock',
            'stock' => 'required|integer|min:0',
            'kategori_id' => 'required|array', // Pastikan kategori_id adalah array
            'kategori_id.*' => 'exists:kategori,kategori_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Proses cover image jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $newName = 'cover_' . time() . '.' . $image->getClientOriginalExtension(); // Generate a unique file name
            $image->storeAs('covers', $newName); // Adjust the storage path as needed
        } else {
            $newName = ''; // Default cover image name if no image is uploaded
        }

        // Tentukan status berdasarkan stok
        $status = $request->stock > 0 ? 'In Stock' : 'Out Stock';

        // Buat buku baru dengan data yang diterima
        $book = new Buku();
        $book->judul = $request->judul;
        $book->penerbit = $request->penerbit;
        $book->sinopsis = $request->sinopsis;
        $book->status = $status;
        $book->stock = $request->stock;
        $book->cover = $newName;
        $book->save();

        // Menyinkronkan relasi dengan kategori
        $book->kategori()->sync($request->kategori_id);

        Alert::success('Success', 'Book added successfully!');
        return redirect('admin.books');
    }

    public function edit($id)
    {
        $book = Buku::findOrFail($id);
        $categories = Kategori::all();
        return view('admin.books', compact('book', 'categories'));
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
        $book->sinopsis = $request->sinopsis;
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

    public function destroyData($id)
    {
        try {
            $buku = Buku::findOrFail($id);

            // Hapus entri terkait dalam tabel pivot
            $buku->kategori()->detach();

            // Hapus buku
            $buku->delete();

            Alert::success('Success', 'Buku berhasil dihapus!');
            return response()->json(['success' => 'Buku berhasil dihapus!'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
