<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index_admin () {
        return view('index_admin');
    }
    
    public static function getAll()
    {
        return Role::all();
    }
}
