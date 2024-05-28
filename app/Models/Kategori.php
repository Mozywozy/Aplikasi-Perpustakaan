<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori'
    ];
    protected $table = 'kategori';
    protected $primaryKey = 'kategori_id';

    public $timestamps = false;

    // public function bukus()
    // {
    //     return $this->hasMany(Buku::class);
    // }

    public function buku()
    {
        return $this->belongsToMany(Buku::class, 'book_category', 'kategori_id', 'buku_id');
    }
}
