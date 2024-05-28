<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('./assets/css/navbar-customer.css') }}">
    <link rel="stylesheet" href="{{ asset('./assets/css/first-content.css') }}">
    <link rel="stylesheet" href="{{ asset('./assets/css/second-content.css') }}">
    <link rel="stylesheet" href="{{ asset('./assets/css/allbook.css') }}">
    <link rel="stylesheet" href="{{ asset('./assets/css/profile.css') }}">
    <link rel="import" href="/public/images/ilustration.jpg">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Perpustakaan | @yield('title')</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-gradient-dark" style=" margin: auto;">
        <div class="container-fluid" style="max-width: 1200px;">
            <a class="navbar-brand text-light" href="#">Perpustakaan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto"> <!-- Tambahkan kelas ms-auto di sini -->
                    <li class="nav-item ">
                        <a class="nav-link text-light" aria-current="page"
                            href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('allBook') }}">All Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('profile') }}">Profile</a>
                    </li>
                    {{-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdownMenuLink"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @foreach ($categories as $category)
                                <li><a class="dropdown-item" href="#">{{ $category->nama_kategori }}</a></li>
                            @endforeach
                        </ul>
                    </li> --}}
                    <li class="nav-item">
                        <button class="nav-link text-light" href="#" onclick="confirmLogout()">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container-fluid">
        @yield('content')
    </div>

    {{-- <div class="container-fluid">
        @yield('allbook')
        @foreach ($books as $book)
    <div class="modal fade" id="pinjamModal_{{$book->buku_id}}" tabindex="-1" aria-labelledby="pinjamModalLabel" aria-hidden="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pinjamModalLabel">Peminjaman Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <form action="{{ route('peminjaman.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="buku_id" id="buku_id" value="{{ $book->buku_id }}">
                        <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                        <input type="date" class="form-control" id="tanggal_pengembalian" name="tanggal_pengembalian">
                        <label for="stock" class="mt-2">Stock</label>
                        <label for="stock" class="form-label">{{ $book->stock }}</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success">Pinjam Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

    </div> --}}

    <footer class="footer mt-4 py-3 bg-gradient-dark text-white">
        <div class="container text-center">
            <span>© 2024 Perpustakaan. All rights reserved.</span>
        </div>
    </footer>

    @include('sweetalert::alert')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        function confirmLogout() {
            // Tampilkan Sweet Alert
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                // Jika pengguna mengklik "Yes, logout", arahkan ke halaman logout
                if (result.isConfirmed) {
                    window.location.href = "{{ route('logout') }}";
                }
            });
        }

        $(document).ready(function() {
            $('.navbar-nav .nav-link').on('click', function() {
                $('.navbar-nav .nav-link').removeClass('active');
                $(this).addClass('active');
            });

            // Menandai item yang aktif berdasarkan URL saat ini
            var current = window.location.href;
            $('.navbar-nav .nav-link').each(function() {
                if (this.href === current) {
                    $(this).addClass('active');
                }
            });
        });

    </script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
</body>

</html>
