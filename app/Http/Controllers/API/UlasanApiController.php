<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UlasanBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class UlasanApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'buku_id' => 'required|exists:bukus,buku_id',
            'ulasan' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        UlasanBuku::create([
            'user_id' => $request->user_id,
            'buku_id' => $request->buku_id,
            'ulasan' => $request->ulasan,
            'rating' => $request->rating,
        ]);

        return response()->json(['message' => 'Ulasan berhasil dikirim'], 201);
    }

    public function getUlasan()
    {
        $ulasan = UlasanBuku::all();
        return response()->json($ulasan);
    }
}
