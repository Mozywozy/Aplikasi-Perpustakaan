@extends('layouts.layout')

@section('title', 'Category')

@section('content')
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
    </style>

    <div class="container-fluid mt-4">
        <a href="" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDataModal">Add
            Data</a>
        <table id="table" data-url="{{ route('getAllCategory') }}" data-toggle="table" data-search="true" data-pagination="true">
            <thead>
                <tr>
                    <th data-formatter="iterator">No</th>
                    <th data-field="nama_kategori">Nama Kategori</th>
                    <th data-formatter="buttonFormatter">Action</th>
                </tr>
            </thead>
            {{-- <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$category->nama}}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody> --}}
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your form for adding data -->
                    <form action="{{ route('admin.category') }}" method="post" id="addDataForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" placeholder="Tulis disini..." id="nama_kategori"
                                name="nama_kategori">
                        </div>
                        <button type="submit" id="btn-create" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <!-- Modal -->
    <div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your form for editing data -->
                    <form action="{{ route('admin.category') }}" method="post" id="editDataForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori">
                        </div>
                        <!-- Add other input fields for editing data -->
                        <input type="hidden" id="edit_kategori_id" name="kategori_id">
                        <button type="submit" id="btn-update" class="btn btn-primary">Submit</button>
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

        // $('#table').bootstrapTable({
        //     url: "{{ route('getAllCategory') }}",
        //     pagination: true,
        //     search: true
        // })

        function iterator(value, row, index){
            return index + 1;
        }


        function buttonFormatter(value, row) {
            return `<a href="#" class='btn btn-warning btn-edit' data-id='${row.kategori_id}' data-nama-kategori='${row.nama_kategori}'>Edit</a>` +
                ` <button class='btn btn-danger' data-id='${row.kategori_id}' onclick='deleteData(this)'>Delete</button>`;
        }

        // Event handler for edit button
        $(document).on('click', '.btn-edit', function() {
            var kategoriId = $(this).data('id');
            var namaKategori = $(this).data('nama-kategori');
            // Set value of edit_nama_kategori input field
            $('#edit_nama_kategori').val(namaKategori);
            // Set value of edit_kategori_id hidden input field
            $('#edit_kategori_id').val(kategoriId);
            // Show the edit modal
            $('#editDataModal').modal('show');
        });

        $('#editDataForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('admin.category') }}/" + $('#edit_kategori_id').val(),
                type: "POST",
                data: formData,
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    // Close the modal
                    $('#editDataModal').modal('hide');
                    // Refresh table or update UI as needed
                    //  $('#table').bootstrapTable('refresh');
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(xhr.responseText);
                }
            });
        });

        // Function to handle delete action
        function deleteData(button) {
            var categoryId = $(button).data('id');

            // Prompt confirmation dialog using SweetAlert
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
                    // Send DELETE request to server
                    $.ajax({
                        url: "{{ url('kategori/delete') }}/" + categoryId,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Handle success response
                            // console.log(response);
                            // Swal.fire(
                            //     'Deleted!',
                            //     'Your data has been deleted.',
                            //     'success'
                            // );
                            // setTimeout(function() {
                            // }, 2000);
                            // Refresh table or update UI as needed
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        }




        // Function to populate the edit modal with data
        // function populateEditModal(kategoriId) {
        //     // Assuming you have an API endpoint to fetch data for editing
        //     $.get(`{{ route('admin.category', ['kategori' => ':kategori']) }}`.replace(':kategori', kategoriId), function(response) {
        //         $('#edit_nama_kategori').val(response.nama_kategori);
        //         // Populate other fields as needed
        //         $('#edit_kategori_id').val(kategoriId);
        //         // Show the edit modal
        //         $('#editDataModal').modal('show');
        //     });
        // }
    </script>
@endpush
