<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function getAll()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function destroyData($id)
    {
        try {
            $user = User::findOrFail($id);
            // Delete related records in the peminjaman table
            $user->peminjaman()->delete();
            $user->delete();

            Alert::success('Success', 'User Behasil di hapus!');
            return $user;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
