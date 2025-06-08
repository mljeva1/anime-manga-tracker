@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Flash poruke -->
    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(40, 167, 69, 0.3); border: 1px solid #28a745; color: #f5dad3;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="color: #f5dad3;">{{ $collection->name }}</h1>
            @if($collection->description)
                <p style="color: #e6e3e8;">{{ $collection->description }}</p>
            @endif
            <span class="badge" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                {{ ucfirst($collection->type) }} kolekcija
            </span>
        </div>
        <a href="{{ route('collections.index') }}" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-2"></i>Nazad
        </a>
    </div>
    
    <!-- Items - filtriraj po tipu kolekcije -->
    @php
        $filteredItems = $collection->items;
        
        // Ako je kolekcija specifična za anime ili manga, prikaži samo taj tip
        if ($collection->type === 'anime') {
            $filteredItems = $collection->items->where('type', 'anime');
        } elseif ($collection->type === 'manga') {
            $filteredItems = $collection->items->where('type', 'manga');
        }
        // Za 'mixed' tip, prikaži sve
    @endphp

    @foreach($filteredItems as $loop_index => $item)
        <div class="card mb-4" style="background: rgba(74, 38, 67, 0.6); border: 1px solid rgba(66, 46, 46, 0.2); border-radius: 15px;">
            <div class="card-body">
                
                @if($item->type === 'anime')
                    <!-- ANIME Item Header with Collapse -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 style="color: #f5dad3; margin: 0;">{{ $item->title }}</h5>
                            <div class="mt-2">
                                <span class="badge me-2" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                    {{ ucfirst($item->type) }}
                                </span>
                                <span class="badge" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Collapse Button -->
                        <button class="btn btn-outline-light btn-sm" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{ $item->id }}" 
                                aria-expanded="false" 
                                aria-controls="collapse{{ $item->id }}">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Collapsible Content for Anime -->
                    <div class="collapse" id="collapse{{ $item->id }}">
                        <div class="mt-3">
                            @if($item->animes->count() > 0)
                                @php
                                    $watched = $item->animes->where('is_watched', true)->count();
                                    $total = $item->animes->count();
                                    $progress = $total > 0 ? ($watched / $total) * 100 : 0;
                                @endphp
                                
                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <strong style="color: #f5dad3;">
                                        Progress: {{ $watched }}/{{ $total }} ({{ round($progress, 1) }}%)
                                    </strong>
                                    <div class="progress mt-2" style="height: 10px; background: rgba(255, 255, 255, 0.1);">
                                        <div class="progress-bar" style="width: {{ $progress }}%; background: rgba(40, 167, 69, 0.8);"></div>
                                    </div>
                                </div>
                                
                                <!-- Episodes Grid -->
                                <div class="row">
                                    @foreach($item->animes->sortBy('episode_number') as $episode)
                                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                            <div class="episode-card" style="background: rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 0.5rem;">
                                                <!-- Episode Info -->
                                                <div class="mb-2">
                                                    <strong style="color: #f5dad3; font-size: 0.9rem;">
                                                        EP {{ $episode->episode_number }}
                                                    </strong>
                                                    <p style="color: #e6e3e8; font-size: 0.8rem; margin: 0; line-height: 1.2;">
                                                        {{ \Illuminate\Support\Str::limit($episode->title ?? "Episode {$episode->episode_number}", 50) }}
                                                    </p>
                                                </div>
                                                
                                                <!-- Toggle Button -->
                                                <form method="POST" action="{{ route('collections.toggle', [$item->id, $episode->episode_number]) }}" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm w-100 {{ $episode->is_watched ? 'btn-success' : 'btn-outline-light' }}">
                                                        @if($episode->is_watched)
                                                            <i class="bi bi-check-circle-fill me-1"></i>Pogledano
                                                        @else
                                                            <i class="bi bi-circle me-1"></i>Nije pogledano
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- No episodes available -->
                                <div class="text-center py-3">
                                    <i class="bi bi-film" style="font-size: 2rem; color: rgba(255, 255, 255, 0.3);"></i>
                                    <p style="color: #e6e3e8; margin: 0;">Nema dostupnih epizoda</p>
                                </div>
                            @endif
                        </div>
                    </div>

                @elseif($item->type === 'manga')
                    <!-- MANGA Item - No Collapse, Direct Layout -->
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Left Side - Manga Info -->
                        <div class="flex-grow-1">
                            <h5 style="color: #f5dad3; margin: 0;">{{ $item->title }}</h5>
                            <div class="mt-2">
                                <span class="badge me-2" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                    <i class="bi bi-book me-1"></i>{{ ucfirst($item->type) }}
                                </span>
                                <span class="badge me-2" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                    {{ ucfirst($item->status) }}
                                </span>
                                @if($item->manga)
                                    <span class="badge" style="background: {{ $item->manga->is_read ? 'rgba(40, 167, 69, 0.8)' : 'rgba(108, 117, 125, 0.8)' }}; color: #f5dad3;">
                                        {{ $item->manga->is_read ? 'Pročitana' : 'Nije pročitana' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right Side - Toggle Button -->
                        <div class="ms-3">
                            @if($item->manga)
                                <form method="POST" action="{{ route('collections.toggle-manga', $item->id) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn {{ $item->manga->is_read ? 'btn-success' : 'btn-outline-light' }}">
                                        @if($item->manga->is_read)
                                            <i class="bi bi-check-circle-fill me-2"></i>Pročitana
                                        @else
                                            <i class="bi bi-circle me-2"></i>Označiti kao pročitanu
                                        @endif
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="bi bi-exclamation-triangle me-2"></i>Nema podataka
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endforeach

    <!-- Empty Collection State -->
    @if($filteredItems->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-collection" style="font-size: 4rem; color: rgba(255, 255, 255, 0.3);"></i>
            <h3 class="mt-3" style="color: #f5dad3;">
                @if($collection->type === 'anime')
                    Nema anime u kolekciji
                @elseif($collection->type === 'manga')
                    Nema manga u kolekciji
                @else
                    Kolekcija je prazna
                @endif
            </h3>
            <p style="color: #e6e3e8;">
                @if($collection->type === 'anime')
                    Dodaj anime u ovu kolekciju.
                @elseif($collection->type === 'manga')
                    Dodaj manga u ovu kolekciju.
                @else
                    Dodaj anime ili manga u ovu kolekciju.
                @endif
            </p>
            <div class="mt-3">
                @if($collection->type === 'anime' || $collection->type === 'mixed')
                    <a href="{{ route('anime.index') }}" class="btn btn-outline-light me-2">
                        <i class="bi bi-search me-2"></i>Pretraži anime
                    </a>
                @endif
                @if($collection->type === 'manga' || $collection->type === 'mixed')
                    <a href="{{ route('manga.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-book me-2"></i>Pretraži manga
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.episode-card:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    transition: background 0.2s ease;
}

.collapse {
    transition: all 0.3s ease;
}

/* Hover efekt za manga kartice */
.card:has(.flex-grow-1):hover {
    background: rgba(74, 38, 67, 0.7) !important;
    transition: background 0.2s ease;
}
</style>
@endsection