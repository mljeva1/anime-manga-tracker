@extends('layouts.app')

@section('content')
<style>
.collection-card {
    background: rgba(74, 38, 67, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    height: 100%; /* Sve kartice iste visine */
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
}

.collection-card:hover {
    background: rgba(74, 38, 67, 0.7);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.collection-card-body {
    display: flex;
    flex-direction: column;
    flex: 1; /* Omogućuje da se card-body širi */
    padding: 1.5rem;
}

.collection-description {
    flex-grow: 1; /* Opis će zauzeti dostupan prostor */
    margin-bottom: 1rem;
}
</style>

<div class="container my-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="color: #f5dad3;">Moje Kolekcije</h1>
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-2"></i>Nova Kolekcija
        </button>
    </div>
    
    <!-- Collections Grid -->
    <div class="row">
        @foreach($collections as $collection)
            <div class="col-lg-4 col-md-6 mb-4"> <!-- Bolje responsive breakpoints -->
                <div class="collection-card">
                    <div class="collection-card-body">
                        <!-- Naslov -->
                        <h5 style="color: #f5dad3; margin-bottom: 1rem;">{{ $collection->name }}</h5>
                        
                        <!-- Opis - fleksibilni prostor -->
                        <div class="collection-description">
                            @if($collection->description)
                                <p style="color: #e6e3e8; margin: 0; line-height: 1.4;">
                                    {{ \Illuminate\Support\Str::limit($collection->description, 100) }}
                                </p>
                            @else
                                <p style="color: rgba(230, 227, 232, 0.7); margin: 0; font-style: italic;">
                                    Nema opisa
                                </p>
                            @endif
                        </div>
                        
                        <!-- Badges -->
                        <div class="mb-3">
                            <span class="badge me-2" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                <i class="bi bi-{{ $collection->type === 'anime' ? 'play-circle' : ($collection->type === 'manga' ? 'book' : 'collection') }} me-1"></i>
                                {{ ucfirst($collection->type) }}
                            </span>
                            <span class="badge" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                {{ $collection->items_count }} 
                                {{ $collection->items_count == 1 ? 'stavka' : 'stavki' }}
                            </span>
                        </div>
                        
                        <!-- Gumb - uvijek na dnu -->
                        <div class="mt-auto">
                            <a href="{{ route('collections.show', $collection->id) }}" 
                               class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>Otvori kolekciju
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($collections->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-collection" style="font-size: 4rem; color: rgba(255, 255, 255, 0.3);"></i>
            <h3 class="mt-3" style="color: #f5dad3;">Nemaš još nijednu kolekciju</h3>
            <p style="color: #e6e3e8;">Stvori svoju prvu kolekciju anime ili manga!</p>
            <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle me-2"></i>Stvori prvu kolekciju
            </button>
        </div>
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: rgba(74, 38, 67, 0.9); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px;">
            <form method="POST" action="{{ route('collections.store') }}">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                    <h5 class="modal-title" id="createModalLabel" style="color: #f5dad3;">
                        <i class="bi bi-plus-square me-2"></i>Nova Kolekcija
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="collection_name" class="form-label" style="color: #f5dad3;">Naziv kolekcije:</label>
                        <input type="text" id="collection_name" name="name" class="form-control" 
                               placeholder="Moja kolekcija" required
                               style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                    </div>
                    <div class="mb-3">
                        <label for="collection_type" class="form-label" style="color: #f5dad3;">Tip kolekcije:</label>
                        <select id="collection_type" name="type" class="form-select" required
                                style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                            <option value="anime">🎬 Anime</option>
                            <option value="manga">📚 Manga</option>
                            <option value="mixed">🎯 Miješano</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="collection_description" class="form-label" style="color: #f5dad3;">Opis (opcionalno):</label>
                        <textarea id="collection_description" name="description" class="form-control" rows="3"
                                  placeholder="Kratki opis tvoje kolekcije..."
                                  style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Odustani</button>
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-check-circle me-2"></i>Stvori kolekciju
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
