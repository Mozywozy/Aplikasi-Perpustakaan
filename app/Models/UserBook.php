<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
    use HasFactory;

    protected $table = 'user_book'; // Sesuaikan dengan nama tabel Anda jika berbeda

    protected $fillable = [
        'user_id',
        'buku_id',
        'status', // Tambahkan kolom status untuk melacak status peminjaman buku
    ];

    // Definisikan relasi antara UserBook dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Definisikan relasi antara UserBook dengan Book
    public function book()
    {
        return $this->belongsTo(Buku::class);
    }
}
