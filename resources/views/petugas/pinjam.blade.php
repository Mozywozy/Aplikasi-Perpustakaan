@extends('layouts.petugas')

@section('title', 'Peminjaman')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Nama Peminjam</th>
                <th>Judul Buku</th>
                <th>Tanggal Pengembalian</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peminjamanSemua as $peminjaman)
            <tr>
                <td>{{ $peminjaman->user->username }}</td>
                <td>{{ $peminjaman->buku->judul }}</td>
                <td>{{ $peminjaman->tanggal_pengembalian }}</td>
                <td>
                    @if($peminjaman->status == 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($peminjaman->status == 'approved')
                    <span class="badge bg-success">Approved</span>
                    @elseif($peminjaman->status == 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                    @elseif($peminjaman->status == 'buku harus dikembalikan')
                    <span class="badge bg-danger">Buku harus dikembalikan</span>
                    @elseif($peminjaman->status == 'buku sudah dikembalikan')
                    <span class="badge bg-secondary">Buku sudah dikembalikan</span>
                    @endif
                </td>
                <td>
                    @if($peminjaman->status == 'pending')
                    <button type="button" class="btn btn-success btn-approve" data-bs-toggle="modal"
                        data-bs-target="#approveModal_{{ $peminjaman->peminjaman_id }}"
                        data-peminjaman-id="{{ $peminjaman->peminjaman_id }}">Setujui</button>
                    <form action="{{ route('peminjaman.reject', ['id' => $peminjaman->peminjaman_id]) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </form>
                    @elseif($peminjaman->status == 'approved' || $peminjaman->status == 'buku harus dikembalikan')
                    <button type="button" class="btn btn-primary btn-return" data-bs-toggle="modal"
                        data-bs-target="#returnModal_{{ $peminjaman->peminjaman_id }}"
                        data-peminjaman-id="{{ $peminjaman->peminjaman_id }}">Buku dikembalikan</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Approve -->
@foreach ($peminjamanSemua as $peminjaman)
<div class="modal fade" id="approveModal_{{ $peminjaman->peminjaman_id }}" tabindex="-1"
    aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Setujui Peminjaman Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm_{{ $peminjaman->peminjaman_id }}"
                action="{{ route('peminjaman.approve', ['id' => $peminjaman->peminjaman_id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->peminjaman_id }}">
                    <label for="tanggal_pengembalian_{{ $peminjaman->peminjaman_id }}" class="form-label">Tanggal
                        Pengembalian</label>
                    <input type="date" class="form-control" id="tanggal_pengembalian_{{ $peminjaman->peminjaman_id }}"
                        name="tanggal_pengembalian" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Return -->
@foreach ($peminjamanSemua as $peminjaman)
<div class="modal fade" id="returnModal_{{ $peminjaman->peminjaman_id }}" tabindex="-1"
    aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Pengembalian Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="returnForm_{{ $peminjaman->peminjaman_id }}"
                action="{{ route('peminjaman.return', ['id' => $peminjaman->peminjaman_id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->peminjaman_id }}">
                    <label for="kondisi_buku_{{ $peminjaman->peminjaman_id }}" class="form-label">Kondisi Buku</label>
                    <select class="form-control kondisi-buku" id="kondisi_buku_{{ $peminjaman->peminjaman_id }}"
                        name="kondisi_buku" required>
                        <option value="Normal">Normal</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Hilang">Hilang</option>
                        <option value="Telat">Telat</option>
                    </select>
                    <div class="mt-3 d-none" id="telatInfo"> <!-- Hidden by default -->
                        <label for="jumlah_hari_telat_{{ $peminjaman->peminjaman_id }}" class="form-label">Jumlah Hari Telat</label>
                        <input type="number" class="form-control" id="jumlah_hari_telat_{{ $peminjaman->peminjaman_id }}" name="jumlah_hari_telat">
                    </div>  
                    <div class="mt-3">
                        <label for="denda_{{ $peminjaman->peminjaman_id }}" class="form-label">Denda</label>
                        <input type="text" class="form-control" id="denda_{{ $peminjaman->peminjaman_id }}" name="denda"
                            readonly>
                    </div>
                    <input type="hidden" class="form-control tanggal-pengembalian" id="tanggal_pengembalian_{{ $peminjaman->peminjaman_id }}"
                        value="{{ $peminjaman->tanggal_pengembalian }}" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@include('sweetalert::alert')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.form-select').select2();
        $('.kondisi-buku').on('change', function () {
            var kondisiBuku = $(this).val();
            var modalBody = $(this).closest('.modal-body');
            var dendaInput = modalBody.find('input[name="denda"]');
            var jumlahHariTelatInput = modalBody.find('input[name="jumlah_hari_telat"]');
            var tanggalPengembalian = modalBody.find('.tanggal-pengembalian').val();
            var denda = 0;

            if (kondisiBuku === 'Rusak') {
                denda += 30000;
            } else if (kondisiBuku === 'Hilang') {
                denda += 100000;
            } else if (kondisiBuku === 'Telat') {
                // Tampilkan elemen untuk memasukkan jumlah hari telat
                modalBody.find('#telatInfo').removeClass('d-none');
                var jumlahHariTelat = parseInt(jumlahHariTelatInput.val());
                denda += jumlahHariTelat * 5000;
            } else {
                // Sembunyikan elemen untuk memasukkan jumlah hari telat jika bukan "Telat"
                modalBody.find('#telatInfo').addClass('d-none');
            }

            dendaInput.val(denda);
        });

        // Tambahkan penanganan perubahan pada input jumlah hari telat
        $('input[name="jumlah_hari_telat"]').on('input', function() {
            var modalBody = $(this).closest('.modal-body');
            var kondisiBuku = modalBody.find('.kondisi-buku').val();
            var dendaInput = modalBody.find('input[name="denda"]');
            var jumlahHariTelat = parseInt($(this).val());

            if (kondisiBuku === 'Telat') {
                var denda = jumlahHariTelat * 5000;
                dendaInput.val(denda);
            }
        });
    });
</script>
@endpush
