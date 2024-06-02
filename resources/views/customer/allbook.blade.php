@extends('layouts.customer')

@section('title', 'All Books')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4 mt-6 judul-abk">Temukan Bukumu dan Temukan pengalaman menarik!</h2>
                <form action="" method="GET">
                    <div class="input-group mb-3">
                        <select class="form-select custom-border mt-2 me-2 select-kategori" id="kategori" style="padding: 5px"
                            name="kategori">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->kategori_id }}">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control custom-border mt-2" style="padding: 20px;"
                            placeholder="Search..." name="judul">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        @foreach ($books as $book)
            <div class="col-md-3 mb-4">
                <div class="card-wrapper" data-aos="fade-up" data-aos-duration="1000">
                    <div class="card @if ($book->stock == 0) blur-card @endif" style="max-width: 270px;">
                        <div class="rating-overlay">
                            <i class="fas fa-star"></i>{{ number_format($book->average_rating, 1) }}/5
                        </div>
                        <img src="{{ asset('storage/covers/' . $book->cover) }}" class="card-img-top"
                            alt="{{ $book->buku_id }}" draggable="false">
                        <div class="card-body">
                            <p class="card-title" style="font-weight: bold">{{ $book->judul }}</p>
                            <p class="card-text">
                                @foreach ($book->kategori as $kategori)
                                    {{ $kategori->nama_kategori }}
                                    @if (!$loop->last)
                                        |
                                    @endif
                                @endforeach
                            </p>
                            @if ($book->stock > 0)
                                <form action="{{ route('peminjaman.store') }}" method="POST">
                                    @csrf
                                    <a href="{{ route('books.show', ['id' => $book->buku_id]) }}" class="btn btn-primary">Lihat buku</a>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if ($book->stock == 0)
                        <div class="stock-unavailable">Stock habis</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    

    @include('sweetalert::alert')
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            // Custom scripts if any
        </script>
    @endpush

@endsection
