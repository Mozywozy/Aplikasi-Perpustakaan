<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'email',
        'role_id',
        'status',
        'profile_image',
    ];

    protected $table = 'user'; // Menyesuaikan nama tabel dalam basis data
    protected $primaryKey = 'user_id'; // Menyesuaikan nama primary key

    // Menyatakan bahwa tidak ada kolom timestamp di tabel User
    public $timestamps = false;

    // Definisi relasi antara User dan Role
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'user_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function borrowedBooks()
    {
        return $this->belongsToMany(Buku::class, 'user_book')->withPivot('status')->withTimestamps();
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'buku_id');
    }

    public function ulasanBuku()
    {
        return $this->hasMany(UlasanBuku::class, 'user_id', 'user_id');
    }
}
