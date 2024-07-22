@extends('layouts.petugas')

@section('title', 'Petugas')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        #table {
            border: 2px solid #dee2e6;
        }

        #table th,
        #table td {
            border: 2px solid #dee2e6;
        }

        .bootstrap-table .search input {
            border: 3px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }

        .modal-content input {
            border: 2px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }

        .modal-header .btn-close {
            color: #000;
        }

        .active-status {
            color: rgb(12, 209, 12);
        }

        .inactive-status {
            color: rgb(252, 23, 23);
        }

        .modal-body select {
            border: 2px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            margin-bottom: 2rem
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row mt-4 justify-content-center centered-cards">
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div
                            class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">book</i>
                        </div>
                        <div class=" shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10"></i>
                        </div>
                        <div class="text-center pt-1">
                            <p class="text-sm mb-0 text-capitalize">Books</p>
                            <h4 class="mb-0">{{ $book_count }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+1 </span>than last week</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <a href="petugas-add-book" data-url="{{ route('getAllBook') }}" type="button" class="btn btn-success">Add Data</a>
        <div class="table-responsive">
            <table id="table" data-toggle="table" data-search="true" data-pagination="true">
                <thead>
                    <tr>
                        <th data-formatter="iterator">No</th>
                        <th data-field="judul">Judul</th>
                        <th data-field="penerbit">Penerbit</th>
                        <th data-field="status">Status</th>
                        <th data-field="stock">Stock</th>
                        <th data-field="nama_kategori">Kategori</th>
                        <th data-formatter="buttonFormatter">Action</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ url('buku-petugas') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="edit-judul" name="judul"
                                placeholder="Judul disini" style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
                        </div>
                        <div class="mb-3">
                            <label for="penerbit" class="form-label">Penerbit</label>
                            <input type="text" class="form-control" id="edit-penerbit" name="penerbit"
                                placeholder="Penerbit disini" style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
                        </div>
                        <div class="form-group">
                            <label for="sinopsis">Sinopsis</label>
                            <textarea name="sinopsis" id="sinopsis" class="form-control" rows="5" style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;"
                                id="edit-status" name="status">
                                <option value="In Stock">In Stock</option>
                                <option value="Out Stock">Out Stock</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="edit-stock" name="stock" placeholder="Stock"
                                style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Cover</label>
                            <div class="input-group">
                                <input type="file" id="cover" name="image" style="border: 3px solid #dee2e6;">
                            </div>
                            <div class="mt-2">
                                <img id="edit-cover-preview" src="" alt="Cover Image"
                                    style="max-width: 100%; height: auto;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select name="kategori_id[]" id="edit-kategori" class="form-control select-multiple"
                                style="width: 100%" multiple required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->kategori_id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="buku_id" name="buku_id" value="">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    @include('sweetalert::alert')
@endsection

@push('scripts')
    <script src="https://unpkg.com/bootstrap-table@1.22.2/dist/bootstrap-table.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#table').bootstrapTable({
            url: "{{ route('getAllBook') }}",
            pagination: true,
            search: true
        })

        $(document).ready(function() {
            $('.select-multiple').select2();
        });

        function iterator(value, row, index) {
            return index + 1;
        }

        function buttonFormatter(value, row) {
            // Create an array to hold kategori_ids
            var kategoriIds = row.kategori.map(function(k) {
                return k.kategori_id;
            });

            // Assuming row.cover is the cover image path
            var coverImage = row.cover ? `/storage/covers/${row.cover}` : ''; // Adjust path as needed

            return `<button class='btn btn-warning btn-edit'
                    data-id='${row.buku_id}'
                    data-judul='${row.judul}'
                    data-penerbit='${row.penerbit}'
                    data-sinopsis='${row.sinopsis}'
                    data-status='${row.status}'
                    data-stock='${row.stock}'
                    data-cover='${coverImage}'
                    data-kategori='${JSON.stringify(kategoriIds)}'
                    data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>` +
                ` <button class='btn btn-danger' data-id='${row.buku_id}' onclick='deleteData(this)'>Delete</button>` + ` <a href='/book/${row.buku_id}/reviews' class='btn btn-info'>Reviews</a> `;
        }


        // Handler untuk klik tombol edit
        $(document).on('click', '.btn-edit', function() {
            var bukuId = $(this).data('id');
            var judul = $(this).data('judul');
            var penerbit = $(this).data('penerbit');
            var sinopsis = $(this).data('sinopsis');
            var status = $(this).data('status');
            var stock = $(this).data('stock');
            var kategori = $(this).data('kategori');
            var cover = $(this).data('cover');

            // Isi nilai-nilai formulir modal dengan data yang diperoleh
            $('#buku_id').val(bukuId);
            $('#edit-judul').val(judul);
            $('#edit-penerbit').val(penerbit);
            $('#edit-sinopsis').val(sinopsis);
            $('#edit-status').val(status);
            $('#edit-stock').val(stock);
            $('#edit-kategori').val(kategori).trigger('change'); // Pilih kategori menggunakan Select2

            // Set the cover image URL if available
            if (cover) {
                $('#edit-cover-preview').attr('src', cover);
            } else {
                $('#edit-cover-preview').attr('src', ''); // Clear the image if not available
            }
        });

        $('#editForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var bookId = $('#buku_id').val();

            $.ajax({
                url: "/buku-petugas/" + bookId,
                type: "POST",
                enctype: 'multipart/form-data',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    console.log(response);
                    $('#editModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        function deleteData(button) {
            var bookId = $(button).data('id');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Data ini akan terhapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('petugas/book/delete') }}/" + bookId,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Swal.fire(
                            //     'Deleted!',
                            //     'Your data has been deleted.',
                            //     'success'
                            // );
                            // setTimeout(function() {
                            // }, 2000);
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        }
    </script>
@endpush
