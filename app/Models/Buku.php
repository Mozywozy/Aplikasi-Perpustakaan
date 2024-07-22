<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'penerbit',
        'sinopsis',
        'status',
        'stock',
        'jenis',
        'cover',
    ];
    protected $table = 'buku'; // Menyesuaikan nama tabel dalam basis data
    protected $primaryKey = 'buku_id'; // Menyesuaikan nama primary key

    // Menyatakan bahwa tidak ada kolom timestamp di tabel Buku
    public $timestamps = false;

    // Definisi relasi antara Buku dan Kategori
    // public static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($model) {
    //         if (is_null($model->kategori_id)) {
    //             throw new \Exception('Kategori harus dipilih.');
    //         }
    //     });
    // }
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id');
    }

    public function kategori()
    {
        return $this->belongsToMany(Kategori::class, 'book_category', 'buku_id', 'kategori_id');
    }

    public function borrowers()
    {
        return $this->belongsToMany(User::class, 'user_book')->withPivot('status')->withTimestamps();
    }

    public function getKategoriNamesAttribute()
    {
        return $this->kategori->pluck('nama_kategori')->implode(', ');
    }

    public function ulasanBuku()
    {
        return $this->hasMany(UlasanBuku::class, 'buku_id', 'buku_id');
    }
}
