@extends('layouts.customer')

@section('title', 'All Books')

@section('content')

    <style>
        .custom-border {
            height: 40px;
            border: 1px solid #cdd0d3;
            outline: none;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }

        .custom-border:focus {
            border-color: #ced4da;
            box-shadow: none;
        }

        .select-kategori {
            height: 40px;
        }

        .blur-card {
            filter: blur(2px);
            pointer-events: none;
            opacity: 0.6;
        }

        .stock-unavailable {
            position: absolute;
            top: 50%;
            left: 38%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .card-wrapper {
            position: relative;
        }

        .card-stock-habis {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

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
                                    <input type="hidden" name="buku_id" value="{{ $book->buku_id }}">
                                    <button type="submit" class="btn btn-primary">Pinjam Buku</button>
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
    </div>

    @include('sweetalert::alert')
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            // Custom scripts if any
        </script>
    @endpush

@endsection
