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
