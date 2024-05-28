<?php

namespace App\Http\Controllers;

use App\Models\UlasanBuku;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UlasanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,user_id',
            'buku_id' => 'required|exists:buku,buku_id',
            'ulasan' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        UlasanBuku::create([
            'user_id' => $request->user_id,
            'buku_id' => $request->buku_id,
            'ulasan' => $request->ulasan,
            'rating' => $request->rating,
        ]);

        Alert::success('Berhasil', 'Ulasan berhasil dikirim');
        return back();
    }

    public function getUlasan()
    {    
        $ulasan = UlasanBuku::all();
        return response()->json($ulasan);
    }
}
