@foreach ($peminjaman as $peminjaman)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $peminjaman->user->username }}</td>
        <td>{{ $peminjaman->buku->judul }}</td>
        <td>{{ $peminjaman->tanggal_peminjaman }}</td>
        <td>{{ $peminjaman->tanggal_pengembalian }}</td>
        <td>{{ ucfirst($peminjaman->status) }}</td>
    </tr>
@endforeach