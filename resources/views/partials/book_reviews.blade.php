<div>
    @foreach ($reviews as $review)
        <div class="review">
            <p><strong>{{ $review->user->username }}</strong> ({{ $review->rating }}/5)</p>
            <p>{{ $review->ulasan }}</p>
        </div>
        <hr>
    @endforeach
</div>
