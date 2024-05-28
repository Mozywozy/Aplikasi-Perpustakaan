<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UlasanBuku extends Model
{
    use HasFactory;

    protected $table = 'ulasanBuku';

    // Primary key dari tabel
    protected $primaryKey = 'ulasan_id';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'buku_id',
        'ulasan',
        'rating'
    ];

    public $timestamps = false;

    /**
     * Relasi dengan model User (setiap ulasan dimiliki oleh satu user).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi dengan model Buku (setiap ulasan terkait dengan satu buku).
     */
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'buku_id');
    }

    public static function getAverageRatingPerBook()
    {
        return self::select('buku_id', DB::raw('AVG(rating) as average_rating'))
            ->groupBy('buku_id')
            ->get();
    }
}
