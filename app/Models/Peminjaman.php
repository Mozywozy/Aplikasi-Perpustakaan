<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman'; 
    protected $primaryKey = 'peminjaman_id'; 

    protected $appends = ['status_segera_kembali'];

    public function getStatusSegeraKembaliAttribute()
    {
        $today = Carbon::today();
        if ($this->tanggal_pengembalian < $today && $this->status == 'approved') {
            return 'Buku harus dikembalikan segera!';
        }

        return $this->status;
    }

    protected $fillable = [
        'buku_id',
        'user_id',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'status',
        'kondisi_buku',
        'denda',
    ];

    protected $dates = ['tanggal_peminjaman', 'tanggal_pengembalian'];

    // Set default value for tanggal_peminjaman
    public function setTanggalPeminjamanAttribute($value)
    {
        $this->attributes['tanggal_peminjaman'] = $value ?: now();
    }

    public $timestamps = false;

    public function ulasan()
    {
        return $this->hasOne(UlasanBuku::class, 'peminjaman_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'buku_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
