@extends('layouts.customer')

@section('title', 'Detail Buku')

@section('content')

    <style>
        .rating i {
            color: #ffc107;
            /* Warna bintang aktif */
            font-size: 1.2rem;
            /* Ukuran ikon bintang */
            margin-right: 3px;
            /* Jarak antara ikon bintang */
        }

        .rating i:last-child {
            margin-right: 0;
            /* Menghapus margin kanan pada ikon bintang terakhir */
        }

        /* Gaya tambahan sesuai kebutuhan */
        ul.list-unstyled {
            padding-left: 0;
            /* Menghapus padding kiri pada daftar tanpa gaya */
        }

        ul.list-unstyled li {
            border-bottom: 1px solid #ccc;
            /* Garis pemisah antara setiap item dalam daftar */
            padding-bottom: 15px;
            /* Ruang di bawah setiap item dalam daftar */
        }

        ul.list-unstyled li:last-child {
            border-bottom: none;
            /* Menghapus garis di item terakhir dalam daftar */
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <img src="{{ asset('storage/covers/' . $book->cover) }}" alt="{{ $book->judul }}" class="img-fluid">
            </div>
            <div class="col-md-8">
                <h2>{{ $book->judul }}</h2>
                <p>Penerbit: {{ $book->penerbit }}</p>
                <p>Stock: {{ $book->stock }}</p>
                <!-- Form pinjam buku -->
                <form action="{{ route('peminjaman.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="buku_id" value="{{ $book->buku_id }}">
                    <button type="submit" class="btn btn-primary">Pinjam Buku</button>
                </form>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Ulasan</h3>
                <ul class="list-unstyled">
                    @if (isset($book->ulasanBuku) && $book->ulasanBuku->isNotEmpty())
                        @foreach ($book->ulasanBuku as $review)
                            <li class="mb-3">
                                <div class="d-flex align-items-center">
                                    <strong>{{ $review->user->username }}</strong> -
                                    <div class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if ($review->ulasan)
                                    <p>{{ $review->ulasan }}</p>
                                @endif
                            </li>
                        @endforeach
                    @else
                        <p>Belum ada ulasan untuk buku ini.</p>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection
