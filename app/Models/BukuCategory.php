<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuCategory extends Model
{
    use HasFactory;
    protected $fillable = ['buku_id', 'kategori_id'];
    protected $table = 'book_category';
    protected $primaryKey = 'id';

    // Definisikan relasi ke model Buku
     // Relationship with Buku
     public function buku()
     {
         return $this->belongsTo(Buku::class, 'buku_id');
     }
 
     // Relationship with Kategori
     public function kategori()
     {
         return $this->belongsTo(Kategori::class, 'kategori_id');
     }
    
}
