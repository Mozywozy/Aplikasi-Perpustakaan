<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Kategori::all();
        return view('admin.category', ['categories' => $categories]);
    }

    public function getAll()
    {
        // return Kategori::all();
        $categories = Kategori::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|unique:kategori|max:50'
        ]);
        // Jika validasi gagal, tampilkan SweetAlert
        if ($validator->fails()) {
            Alert::error('Error', 'Category already exists!');
            return redirect('admin.category');
        }

        $caregories = Kategori::create($request->all());
        Alert::success('Success', 'Category added successfully!');

        return redirect('admin.category');
    }

    public function updateData(Request $request, $id)
    {
        // Validasi input
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

}
