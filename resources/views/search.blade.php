@extends('layouts.app')

@section('title', 'Rezultati pretrage')

@section('content')
<style>
.card {
    will-change: auto;
    transform: translateZ(0);
    height: 450px;
}
.container { will-change: auto; }
.row { contain: layout style; }

.content-toggle {
    background: rgba(74, 38, 67, 0.4);
    border-radius: 26px;
    padding: 5px;
    display: inline-flex;
    margin-bottom: 20px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.content-toggle .btn {
    border-radius: 20px;
    color: #e6e3e8;
    border: none;
    padding: 8px 25px;
    margin: 2px;
}

.content-toggle .btn.active {
    background: rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    color: #f5dad3;
}

.content-toggle .btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Ograniči opis na 3 reda */
.card-text-limited {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    max-height: calc(1.4em * 3); /* 3 reda * line-height */
}

/* Flexbox za card body da se elementi poravnaju */
.card-body-flex {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.card-bottom-row {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Forsiraj istu visinu za sve slike - najjači override */
.card .card-img-top {
    height: 250px !important;
    width: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
    border-radius: 15px 15px 0 0 !important;
    display: block !important;
    max-height: 250px !important;
    min-height: 250px !important;
}

/* Backup za slučaj da nema slike */
.card-img-top {
    background-color: rgba(74, 38, 67, 0.3) !important;
}

</style>

<div class="container-fluid my-5">    
    @if($query)
    
    <!-- Naslov -->
    <div class="row justify-content-center mb-4">
        <div class="col-auto">
            <h2 class="ps-3 pe-3 pt-2 pb-2"
                style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 26px;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.2);">
                Rezultati pretrage za: <span style="color: #f5dad3;">{{ $query }}</span>
            </h2>
        </div>
    </div>

    <!-- Filter Toggle - centriran iznad sadržaja -->
    <div class="row justify-content-center mb-4">
        <div class="col-auto">
            <div class="content-toggle" role="group" aria-label="Content type toggle">
                <input type="radio" class="btn-check" name="typeFilter" id="animeFilter" value="anime" 
                       {{ $currentType === 'anime' ? 'checked' : '' }} autocomplete="off">
                <label class="btn {{ $currentType === 'anime' ? 'active' : '' }}" for="animeFilter">
                    <i class="bi bi-play-circle me-2"></i>Anime
                </label>
                
                <input type="radio" class="btn-check" name="typeFilter" id="mangaFilter" value="manga"
                       {{ $currentType === 'manga' ? 'checked' : '' }} autocomplete="off">
                <label class="btn {{ $currentType === 'manga' ? 'active' : '' }}" for="mangaFilter">
                    <i class="bi bi-book me-2"></i>Manga
                </label>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row justify-content-center">
        <div class="col-8">
            @if($results->isEmpty())
                <div class="alert alert-warning text-center">Nema rezultata na engleskom jeziku.</div>
            @else
                <div class="row justify-content-center">
                    @foreach($results as $i => $item)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card h-100" style="background: rgba(74, 38, 67, 0.6); color:#e6e3e8 !important; 
                                 border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px;">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" 
                                        class="card-img-top"
                                        style="object-fit: cover; border-radius: 15px 15px 0 0; height: 250px; width: 100%;"
                                        decoding="async"
                                        loading="{{ $i < 3 ? 'eager' : 'lazy' }}"
                                        alt="{{ $item['title'] }}">
                                @endif

                                <div class="card-body card-body-flex">
                                    <h5 class="card-title">{{ $item['title'] }}</h5>
                                    <p class="card-text card-text-limited" style="font-size: 0.95em;">
                                        {{ \Illuminate\Support\Str::limit($item['synopsis'], 150) }}
                                    </p>
                                    <div class="card-bottom-row">
                                        <span class="badge" style="background: rgba(74, 38, 67, 0.6);">{{ $item['type'] }}</span>
                                        @if($item['type'] === 'TV' || $item['type'] === 'Movie' || $item['type'] === 'OVA' || $item['type'] === 'Special')
                                            <a href="{{ route('anime.show', $item['mal_id']) }}" class="btn btn-outline-light btn-sm">Prikaži više</a>
                                        @else
                                            <a href="{{ route('manga.show', $item['mal_id']) }}" class="btn btn-outline-light btn-sm">Prikaži više</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
// JavaScript za filter promjenu
document.querySelectorAll('input[name="typeFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('type', this.value);
        window.location.href = url.toString();
    });
});
</script>

@endsection
