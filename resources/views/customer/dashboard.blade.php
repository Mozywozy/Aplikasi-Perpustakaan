@extends('layouts.customer')

@section('title', 'Home')

@section('content')

<style>
    .rating-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        .rating-overlay i {
            margin-bottom: 3px;
            margin-right: 5px;
        }
</style>

<main>
    {{-- FIRTS CONTENT --}}
    <section class="hero">
        <div class="content">
            <div class="text-content">
                <h1 class="judul">Perpustakaan</h1>
                <p class="kedua">eBook Library.</p>
                <p>Tambah Wawasan mu, jelajahi semua buku yang ada di dunia!!</p>
                <a href="{{ route('allBook') }}" type="button" class="btn btn-primary">See All Books</a>
            </div>
            <div class="image-content">
                <img src="/images/ilustration.jpg" alt="Woman sitting on a stack of books using a laptop" class="hero-image">
            </div>
        </div>
    </section>

    {{-- SECOND CONTENT --}}
    <section class="second-section">
        <div class="content-ss">
            <div class="text-content-ss">
                <h1 class="judul-ss">Recommendation Books</h1>
                <p class="kedua-ss">Temukan buku yang menarik disini!!</p>
            </div>
            <div class="card-content">
                @foreach ($recommendedBooks as $book)
                    <div class="card">
                        <div class="rating-overlay">
                            <i class="fas fa-star"></i>{{ number_format($book->ulasan_buku_avg_rating, 1) }}/5
                        </div>
                        <img src="{{ asset('storage/covers/' . $book->cover) }}" class="card-img-top"
                            alt="{{ $book->judul }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $book->judul }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="btn-sm">
                <a href="{{ route('allBook') }}" type="button" class="btn btn-primary">See More</a>
            </div>
        </div>
    </section>

    {{-- THIRD CONTENT --}}
    <section class="third-section">
        <div class="content-ts">
            <div class="image-content-ts">
                <img src="/images/ilustrasi2.jpg" alt="ilustrasi2" class="hero-image">
            </div>
            <div class="text-content-ts">
                <h2 class="judul-ts">Pinjam Buku disini aja!</h2>
                <p class="kedua-ts">Tersedia banyak sekali buku yang bisa kamu baca setiap harinya!</p>
            </div>
        </div>
    </section>
</main>    

@include('sweetalert::alert')

@endsection
