@extends('layouts.customer')

@section('title', 'Profile')

@section('content')


<div class="container-profile mt-4">
    <div class="row">
        <div class="col-md-4 text-center">
            <div class="profile-picture">
                <img src="{{ $user->profile_image ? asset('storage/public/profile_images/' . $user->profile_image) : asset('images/default-profile.jpg') }}" alt="profile">
            </div>
        </div>
        <div class="col-md-8">
            <h2>{{ $user->username }}</h2>
            <p>{{ $user->email }}</p>
            <button class="btn btn-warning btn-edit-profile" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="container-fluid">
        <h2 class="mt-5">Pinjaman Buku</h2>
        <div class="row mt-4">
            @forelse ($peminjamanPending->merge($peminjamanApproved)->merge($peminjamanRejected)->merge($peminjamanMustReturn)->merge($peminjamanReturned) as $peminjaman)
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="card" @if ($peminjaman->status == 'buku sudah dikembalikan') data-bs-toggle="modal" data-bs-target="#ulasanModal_{{ $peminjaman->buku->buku_id }}" @endif>
                        <div class="position-relative">
                            <img src="{{ asset('storage/covers/' . $peminjaman->buku->cover) }}" class="card-img-top" alt="{{ $peminjaman->buku->judul }}">
                            @if ($peminjaman->status == 'buku sudah dikembalikan')
                                <div class="overlay">
                                    <i class="fas fa-pencil-alt overlay-icon"></i>
                                </div>
                            @endif
                            @if ($peminjaman->status == 'buku sudah dikembalikan' || $peminjaman->status == 'buku harus dikembalikan')
                            <div class="position-absolute top-0 start-0 p-2 text-dark info">
                                <span class="d-block">Kondisi: {{ $peminjaman->kondisi_buku }}</span>
                                <span class="d-block">Denda: Rp{{ number_format($peminjaman->denda, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $peminjaman->buku->judul }}</h5>
                            <p class="card-text">Pengembalian: {{ $peminjaman->tanggal_pengembalian }}</p>
                            <p class="card-text card-status {{ $peminjaman->status }}">
                                Status: {{ ucfirst($peminjaman->status) }}
                            </p>
                            {{-- Kondisi untuk menghilangkan tombol ulasan di sini --}}
                        </div>
                    </div>
                </div>

                <!-- Modal Ulasan -->
                <div class="modal fade" id="ulasanModal_{{ $peminjaman->buku->buku_id }}" tabindex="-1" aria-labelledby="ulasanModalLabel_{{ $peminjaman->buku->buku_id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ulasanModalLabel_{{ $peminjaman->buku->buku_id }}">Tulis Ulasan untuk {{ $peminjaman->buku->judul }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('ulasan.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="buku_id" value="{{ $peminjaman->buku->buku_id }}">
                                    <input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">
                                    <div class="mb-3">
                                        <label for="ulasan_{{ $peminjaman->buku->buku_id }}" class="form-label">Ulasan</label>
                                        <textarea class="form-control custom-border" id="ulasan_{{ $peminjaman->buku->buku_id }}" name="ulasan" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rating_{{ $peminjaman->buku->buku_id }}" class="form-label">Rating</label>
                                        <input type="number" class="form-control custom-border" id="rating_{{ $peminjaman->buku->buku_id }}" name="rating" min="1" max="5" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-12">Tidak ada buku sedang dipinjam.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update', ['id' => $user->user_id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('sweetalert::alert')

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.22.2/bootstrap-table.min.js"></script>

<script>
    $(document).ready(function() {
        $('.btn-edit-profile').click(function() {
            $('#editProfileModal').modal('show');
        });

        $('.card').click(function() {
            var target = $(this).data('bs-target');
            var status = $(this).find('.card-status').text().trim().toLowerCase();

            // Memeriksa apakah status buku adalah "buku sudah dikembalikan" sebelum menampilkan modal
            if (status === 'status: buku sudah dikembalikan') {
                $(target).modal('show');
            } else {
                // Jika bukan, munculkan pesan kesalahan
                alert('Anda hanya dapat memberikan ulasan ketika buku sudah dikembalikan.');
            }
        });
    });

    
</script>
@endpush
