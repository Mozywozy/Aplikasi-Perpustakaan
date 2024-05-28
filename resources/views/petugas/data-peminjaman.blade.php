@extends('layouts.petugas')

@section('title', 'Data Peminjaman')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <div class="mb-3">
        <label for="filterMonth" class="form-label">Pilih Bulan</label>
        <select id="filterMonth" class="form-select">
            <option value="">Semua Bulan</option>
            @foreach (range(1, 12) as $month)
                <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
            @endforeach
        </select>
        <button id="exportToPDF" class="btn btn-primary mt-3">Export to PDF</button>
    </div>
    <div class="table-responsive">
        <table id="peminjamanTable" class="table" data-search="true" data-pagination="true">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Judul Buku</th>
                    <th>Waktu Peminjaman</th>
                    <th>Waktu Pengembalian</th>
                    <th>Kondisi</th>
                    <th>Denda</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="peminjamanData">
                <!-- Data akan dimuat di sini -->
            </tbody>
        </table>
    </div>


    @include('sweetalert::alert')
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.22.2/bootstrap-table.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>

    <script>
        $('#table').bootstrapTable({
            url: "{{ route('PeminjamanPetugas') }}",
            pagination: true,
            search: true
        })

        function iterator(value, row, index) {
            return index + 1;
        }

        $(document).ready(function() {
            loadTableData();

            $('#filterMonth').on('change', function() {
                loadTableData();
            });

            $('#exportToPDF').on('click', function() {
                var month = $('#filterMonth').val();
                var url = "{{ route('petugas.exportPDF') }}?month=" + month;
                window.open(url, '_blank');
            });
        });

        function loadTableData() {
            var month = $('#filterMonth').val();
            $.ajax({
                url: "{{ route('petugas.filter') }}",
                data: {
                    month: month
                },
                success: function(response) {
                    $('#peminjamanData').empty();
                    response.forEach(function(peminjaman, index) {
                        var row = `<tr>
                    <td>${index + 1}</td>
                    <td>${peminjaman.user.username}</td>
                    <td>${peminjaman.buku.judul}</td>
                    <td>${peminjaman.tanggal_peminjaman}</td>
                    <td>${peminjaman.tanggal_pengembalian}</td>
                    <td>${peminjaman.kondisi_buku}</td>
                    <td>${peminjaman.denda}</td>
                    <td>${peminjaman.status}</td>
                </tr>`;
                        $('#peminjamanData').append(row);
                    });
                    $('#peminjamanTable').bootstrapTable('load', response);
                }
            });
        }
    </script>
@endpush
