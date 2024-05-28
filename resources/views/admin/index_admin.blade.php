@extends('layouts.layout')

@section('title', 'Dashboard')

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

    <h3>Page Admin</h3>
    <div class="container-fluid py-4">
        <div class="row mt-4">
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
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div
                            class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">category</i>
                        </div>
                        <div class="text-center pt-1">
                            <p class="text-sm mb-0 text-capitalize">Categories</p>
                            <h4 class="mb-0">{{ $category_count }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+1 </span>than last month
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div
                            class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-icons opacity-10">person</i>
                        </div>
                        <div class="text-center pt-1">
                            <p class="text-sm mb-0 text-capitalize">Users</p>
                            <h4 class="mb-0">{{ $user_count }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">+1</span> than yesterday</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <table id="table" data-url="{{ route('getAllUser') }}" data-toggle="table" data-search="true" data-pagination="true">
            <thead>
                <tr>
                    <th data-formatter="iterator">No</th>
                    <th data-field="username">Nama</th>
                    <th data-field="role_id">Role id</th>
                    <th data-field="status" data-formatter="statusFormatter">Status</th>
                    <th data-formatter="buttonFormatter">Action</th>
                </tr>
            </thead>
        </table>
    </div>

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
                    <form action="{{ route('admin.index_admin') }}" method="post" id="editDataForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_nama_user" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_nama_user" name="username"
                                placeholder="Username">
                        </div>
                        <div class="mb-3">
                          {{-- <label for="edit_status" class="form-label">Status</label> --}}
                          {{-- <input type="text" class="form-control" id="edit_status" name="status"
                              placeholder="Status"> --}}
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <!-- Add other input fields for editing data -->
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <button type="submit" id="btn-update" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@include('sweetalert::alert')
@endsection

@push('scripts')
    <script src="https://unpkg.com/bootstrap-table@1.22.2/dist/bootstrap-table.min.js"></script>
    <script>
        $('#table').bootstrapTable({
            url: "{{ route('getAllUser') }}",
            pagination: true,
            search: true
        })

        function iterator(value, row, index){
            return index + 1;
        }


        function buttonFormatter(value, row) {
            // console.log(row);
            return `<a href="#" class='btn btn-warning btn-edit' data-id='${row.user_id}' data-nama-kategori='${row.user_id}'>Edit</a>` +
                ` <but ton class='btn btn-danger' data-id='${row.user_id}' onclick='deleteData(this)'>Delete</button>`;
        }

        $(document).on('click', '.btn-edit', function() {
            var userId = $(this).data('id');
            var username = $(this).closest('tr').find('td:eq(1)')
                .text(); 
            var status = $(this).closest('tr').find('td:eq(3)')
                .text(); 
            // Set value of edit_nama_user input field
            $('#edit_nama_user').val(username.trim());
            // Set value of edit_status select field
            $('#edit_status').val(status.trim());
            // Set value of edit_user_id hidden input field
            $('#edit_user_id').val(userId);
            // Show the edit modal
            $('#editDataModal').modal('show');
        });

       // Event handler for form submission
       $('#editDataForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('admin.index_admin') }}/" + $('#edit_user_id').val(),
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



        // Event handler for edit button
        // $(document).on('click', '.btn-edit', function() {
        //     var userId = $(this).data('id');
        //     var username = $(this).data('username');
        //     var status = $(this).data('status');
        //     // Set value of edit_nama_kategori input field
        //     $('#edit_username').val(username);
        //     // Set value of edit_nama_kategori input field
        //     $('#edit_status').val(status);
        //     // Set value of edit_kategori_id hidden input field
        //     $('#edit_user_id').val(userId);
        //     // Show the edit modal
        //     $('#editDataModal').modal('show');
        // });


        function statusFormatter(value, row) {
            if (value === 'active') {
                return '<span class="active-status">Active</span>';
            } else if (value === 'inactive') {
                return '<span class="inactive-status">Inactive</span>';
            }
            return value;
        }

         // Function to handle delete action
         function deleteData(button) {
            var userId = $(button).data('id');

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
                        url: "{{ url('admin/delete') }}/" + userId,
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
    </script>
@endpush
