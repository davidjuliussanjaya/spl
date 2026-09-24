<div class="fb-item">
    <div class="fb-quote">"{{ Str::limit($komen->jawaban_text, 150) }}"</div>
    <div class="fb-who">
        @if($isAdmin)
            <strong>{{ $komen->nama_perusahaan ?? 'Anonim' }}</strong>
            @if($komen->responden)<span> &middot; {{ $komen->responden }}</span>@endif
        @else
            <strong>Responden anonim</strong>
        @endif
    </div>
</div>
