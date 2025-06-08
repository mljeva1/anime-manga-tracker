@extends('layouts.app')

@section('title', 'Rezultati pretrage')

@section('content')
<div class="container my-5">
    <h2>Rezultati pretrage za: <span class="text-primary">{{ $query }}</span></h2>
    @if($results->isEmpty())
        <div class="alert alert-warning mt-4">Nema rezultata na engleskom jeziku.</div>
    @else
        <div class="row mt-4">
            @foreach($results as $i => $item)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" 
                            class="card-img-top"
                            width="200" height="300"
                            decoding="async"
                            loading="{{ $i < 3 ? 'eager' : 'lazy' }}"
                            alt="{{ $item['title'] }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $item['title'] }}</h5>
                            <span class="badge bg-secondary mb-2">{{ $item['type'] }}</span>
                            <p class="card-text" style="font-size: 0.95em;">
                                {{ \Illuminate\Support\Str::limit($item['synopsis'], 120) }}
                            </p>
                            <a href="{{ $item['url'] }}" class="btn btn-outline-primary btn-sm">Detalji na MAL</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection