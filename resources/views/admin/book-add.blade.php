@extends('layouts.layout')

@section('title', 'Add Books')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <form action="book-add" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="judul" class="form-label">Judul</label>
            <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul disini"
                style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
        </div>
        <div class="mb-3">
            <label for="penerbit" class="form-label">Penerbit</label>
            <input type="text" class="form-control" id="penerbit" name="penerbit" placeholder="Penerbit disini"
                style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
                <option value="In Stock">In Stock</option>
                <option value="Out Stock">Out Stock</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" placeholder="Stock" style="border: 3px solid #dee2e6; padding: 0.375rem 0.75rem;">
        </div>        
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <div class="input-group">
                <input type="file" id="cover" name="image" style="border: 3px solid #dee2e6;">
                <label class="input-group-text" for="cover"></label>
            </div>
        </div>
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select name="kategori_id[]" id="kategori" class="form-control select-multiple" multiple required>
                <option value="">Pilih Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->kategori_id }}">{{ $category->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>


    @include('sweetalert::alert')
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.22.2/bootstrap-table.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select-multiple').select2();
        })

        
    </script>
@endpush
