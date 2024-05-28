@extends('layouts.rv')

@section('title', 'Book Reviews')

@section('content')
<!-- Include Bootstrap CSS in your main layout file -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <h2>Reviews for {{ $buku->judul }}</h2>
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">Back</a>
    @if($buku->ulasanBuku->isEmpty())
        <p>No reviews yet.</p>
    @else
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Reviewer</th>
                    <th>Rating</th>
                    <th>Ulasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buku->ulasanBuku as $index => $review)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $review->user->username }}</td>
                        <td>{{ $review->rating }}</td>
                        <td>{{ $review->ulasan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
