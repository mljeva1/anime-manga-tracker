@extends('layouts.app')

@section('title', $anime['title_english'])

@section('content')
<style>
.detail-container {
    background: rgba(74, 38, 67, 0.6);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #e6e3e8;
}

.anime-poster {
    border-radius: 15px;
    max-width: 100%;
    height: auto;
}

.info-item {
    margin-bottom: 0.75rem;
}

.info-label {
    color: #f5dad3;
    font-weight: 600;
}

.episodes-container::-webkit-scrollbar {
    width: 6px;
}

.episodes-container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.episodes-container::-webkit-scrollbar-thumb {
    background: rgba(74, 38, 67, 0.6);
    border-radius: 3px;
}

.episode-item:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    transition: background 0.2s ease;
}
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-container p-4">
                <div class="row">
                    <!-- Poster -->
                    <div class="col-md-4 text-center mb-4">
                        <img src="{{ $anime['images']['webp']['large_image_url'] ?? $anime['images']['webp']['image_url'] }}" 
                             alt="{{ $anime['title_english'] }}" class="anime-poster">
                    </div>
                    
                    <!-- Informacije -->
                    <div class="col-md-8">
                        <h1 class="mb-3" style="color: #f5dad3;">{{ $anime['title_english'] }}</h1>
                        @if($anime['title_japanese'])
                            <h2 class="mb-3" style="color: #f5dad3; font-size: 1.2rem;">{{ $anime['title_japanese'] }}</h2>
                        @endif
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-item">
                                    <span class="info-label">Type:</span> {{ $anime['type'] }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Episodes:</span> {{ $anime['episodes'] ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Status:</span> {{ $anime['status'] }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Aired:</span> {{ $anime['aired']['string'] ?? 'N/A' }}
                                </div>
                                
                                <!-- Žanrovi -->
                                @if($anime['genres'])
                                <div class="info-item">
                                    <span class="info-label">Genres:</span>
                                    @foreach($anime['genres'] as $genre)
                                        <span class="badge me-1" style="background: rgba(74, 38, 67, 0.6);">{{ $genre['name'] }}</span>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Studios -->
                                @if($anime['studios'])
                                <div class="info-item">
                                    <span class="info-label">Studios:</span>
                                    @foreach($anime['studios'] as $studio)
                                        <span class="badge me-1" style="background: rgba(74, 38, 67, 0.6);">{{ $studio['name'] }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <div class="info-item">
                                    <span class="info-label">Score:</span> 
                                    @if($anime['score'])
                                        <span class="badge" style="background: rgba(74, 38, 67, 0.6);">{{ $anime['score'] }}/10</span>
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Rank:</span> #{{ $anime['rank'] ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Popularity:</span> #{{ $anime['popularity'] ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Members:</span> {{ number_format($anime['members'] ?? 0) }}
                                </div>
                                @if($anime['trailer']['url'] ?? null)
                                    <a href="{{ $anime['trailer']['url'] }}" class="btn btn-outline-light btn-sm" target="_blank">
                                        Trailer <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                                <!-- Reviews gumb -->
                                <button type="button" class="btn btn-outline-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#reviewsModal">
                                    <i class="bi bi-chat-square-text me-2"></i>Reviews
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Synopsis -->
                @if($anime['synopsis'])
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="info-label">Synopsis</h3>
                        <p style="line-height: 1.6;">{{ $anime['synopsis'] }}</p>
                    </div>
                </div>
                @endif

                @if($anime['background'])
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="info-label">Background</h3>
                        <p style="line-height: 1.6;">{{ $anime['background'] }}</p>
                    </div>
                </div>
                @endif

                <!-- Episodes -->
                @if(!empty($episodes) && is_array($episodes))
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="info-label mb-3">
                            Episodes 
                            @if($totalEpisodes > 0)
                                <small style="color: #f5dad3;">({{ $totalEpisodes }} total)</small>
                            @endif
                        </h3>
                        
                        <!-- Episodes Grid -->
                        <div class="episodes-grid">
                            @foreach($episodes as $episode)
                                @if(is_array($episode) && isset($episode['mal_id']))
                                <div class="episode-item mb-3 p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 col-sm-2">
                                            <span class="badge" style="background: rgba(74, 38, 67, 0.8); font-size: 0.9rem; color: #f5dad3;">
                                                EP {{ $episode['mal_id'] }}
                                            </span>
                                        </div>
                                        <div class="col-md-11 col-sm-10">
                                            <h6 class="mb-1" style="color: #f5dad3;">
                                                {{ $episode['title'] ?? 'Episode ' . ($episode['mal_id'] ?? '') }}
                                            </h6>
                                            @if(isset($episode['title_japanese']) && $episode['title_japanese'])
                                                <p class="mb-1 small" style="color: #f5dad3;">{{ $episode['title_japanese'] }}</p>
                                            @endif
                                            <div class="episode-meta">
                                                @if(isset($episode['aired']) && $episode['aired'])
                                                    <small style="color: #f5dad3;">
                                                        <i class="bi bi-calendar me-1"></i>
                                                        {{ date('M d, Y', strtotime($episode['aired'])) }}
                                                    </small>
                                                @endif
                                                @if(isset($episode['score']) && $episode['score'])
                                                    <small class="ms-3" style="color: #f5dad3;">
                                                        <i class="bi bi-star-fill me-1"></i>
                                                        {{ $episode['score'] }}/10
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Paginacija -->
                        @if($customPagination['last_visible_page'] > 1)
                        <nav class="mt-4" aria-label="Episodes pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                @if($currentPage > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['episodes_page' => $currentPage - 1]) }}" 
                                           style="background: rgba(74, 38, 67, 0.6); border-color: rgba(255, 255, 255, 0.2); color: #f5dad3;">
                                            <i class="bi bi-chevron-left"></i> Prethodna
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" style="background: rgba(74, 38, 67, 0.3); border-color: rgba(255, 255, 255, 0.1); color: #999;">
                                            <i class="bi bi-chevron-left"></i> Prethodna
                                        </span>
                                    </li>
                                @endif

                                <!-- Page Numbers -->
                                @php
                                    $totalPages = $customPagination['last_visible_page'];
                                    $start = max(1, $currentPage - 2);
                                    $end = min($totalPages, $currentPage + 2);
                                @endphp

                                @for($i = $start; $i <= $end; $i++)
                                    @if($i == $currentPage)
                                        <li class="page-item active">
                                            <span class="page-link" style="background: rgba(74, 38, 67, 0.9); border-color: #f5dad3; color: #f5dad3; font-weight: bold;">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['episodes_page' => $i]) }}"
                                               style="background: rgba(74, 38, 67, 0.6); border-color: rgba(255, 255, 255, 0.2); color: #f5dad3;">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                <!-- Next Button -->
                                @if($customPagination['has_next_page'])
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['episodes_page' => $currentPage + 1]) }}"
                                           style="background: rgba(74, 38, 67, 0.6); border-color: rgba(255, 255, 255, 0.2); color: #f5dad3;">
                                            Sljedeća <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" style="background: rgba(74, 38, 67, 0.3); border-color: rgba(255, 255, 255, 0.1); color: #999;">
                                            Sljedeća <i class="bi bi-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>

                        <!-- Info o stranicama -->
                        <div class="text-center mt-3">
                            <small style="color: #f5dad3;">
                                Stranica {{ $currentPage }} od {{ $customPagination['last_visible_page'] }}
                                ({{ count($episodes) }} epizoda na ovoj stranici)
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Gumbovi -->
                <div class="row mt-4">
                    <div class="col-12 text-center">                        
                        @auth
                            <button type="button" class="btn btn-outline-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                Dodaj u kolekciju
                            </button>
                        @endauth
                        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-2"></i>Nazad
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Modal -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="background: rgba(74, 38, 67, 0.9); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                <h5 class="modal-title" id="reviewsModalLabel" style="color: #f5dad3;">
                    <i class="bi bi-chat-square-text me-2"></i>Reviews - {{ $anime['title_english'] }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body" style="color: #e6e3e8; max-height: 70vh; overflow-y: auto;">
                @if(!empty($reviews) && is_array($reviews))
                    @foreach($reviews as $review)
                        @if(is_array($review))
                        <div class="review-item mb-4 p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.2);">
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    @if(isset($review['user']['images']['jpg']['image_url']) && $review['user']['images']['jpg']['image_url'])
                                        <img src="{{ $review['user']['images']['jpg']['image_url'] }}" 
                                             alt="{{ $review['user']['username'] ?? 'User' }}" 
                                             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #f5dad3;">
                                    @else
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(74, 38, 67, 0.8); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="bi bi-person-fill" style="color: #f5dad3; font-size: 1.5rem;"></i>
                                        </div>
                                    @endif
                                    <p class="mt-2 mb-1" style="color: #f5dad3; font-weight: 600; font-size: 0.9rem;">
                                        {{ $review['user']['username'] ?? 'Anonymous' }}
                                    </p>
                                    <small style="color: #f5dad3;">
                                        {{ isset($review['date']) ? date('d.m.Y', strtotime($review['date'])) : 'N/A' }}
                                    </small>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-2">
                                        @if(isset($review['score']) && $review['score'])
                                            <span class="badge" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                                <i class="bi bi-star-fill me-1"></i>{{ $review['score'] }}/10
                                            </span>
                                        @endif
                                        
                                        @if(isset($review['reactions']['overall']) && $review['reactions']['overall'] > 0)
                                            <small class="ms-2" style="color: #f5dad3;">
                                                <i class="bi bi-hand-thumbs-up me-1"></i>
                                                {{ $review['reactions']['overall'] }} helpful
                                            </small>
                                        @endif
                                    </div>
                                    <div class="review-text">
                                        <p style="color: #f5dad3; line-height: 1.5; font-size: 0.9rem;">
                                            {{ \Illuminate\Support\Str::limit($review['review'] ?? 'Nema teksta recenzije.', 600) }}
                                        </p>
                                        @if(strlen($review['review'] ?? '') > 600)
                                            <small style="color: rgba(245, 218, 211, 0.7);">
                                                <i class="bi bi-three-dots"></i> Skraćeno
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 1.5rem 0;">
                        @endif
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-chat-square-text" style="font-size: 3rem; color: rgba(255, 255, 255, 0.3);"></i>
                        <h5 class="mt-3" style="color: #f5dad3;">Nema dostupnih recenzija</h5>
                        <p style="color: #e6e3e8;">Za ovaj anime trenutno nema recenzija.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Zatvori
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add to Collection Modal -->
@auth
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(74, 38, 67, 0.9); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                <h5 class="modal-title" id="addModalLabel" style="color: #f5dad3;">
                    <i class="bi bi-plus-square me-2"></i>Dodaj anime u kolekciju
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Dodaj u postojeću kolekciju -->
                    <div class="col-md-6 mb-4">
                        <div class="card" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px;">
                            <div class="card-body">
                                <h6 style="color: #f5dad3;">Postojeće kolekcije</h6>
                                <form method="POST" action="{{ route('collections.add-anime') }}">
                                    @csrf
                                    <input type="hidden" name="mal_id" value="{{ $anime['mal_id'] }}">
                                    <input type="hidden" name="title" value="{{ $anime['title_english'] }}">
                                    
                                    <div class="mb-3">
                                        <label for="collection_id" class="form-label" style="color: #f5dad3;">Odaberi kolekciju:</label>
                                        <select class="form-select form-select-sm" id="collection_id" name="collection_id" required
                                                style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                                            <option value="">Odaberi kolekciju</option>
                                            @php
                                                // FILTRIRANJE: Prikaži samo anime i mixed kolekcije
                                                $userCollections = Auth::user()->collections()
                                                    ->whereIn('type', ['anime', 'mixed'])
                                                    ->get();
                                            @endphp
                                            @forelse($userCollections as $collection)
                                                @php
                                                    $exists = $collection->items()->where('mal_id', $anime['mal_id'])->exists();
                                                @endphp
                                                <option value="{{ $collection->id }}" {{ $exists ? 'disabled' : '' }}>
                                                    {{ $collection->name }} 
                                                    @if($collection->type === 'mixed')
                                                        (Miješano)
                                                    @else
                                                        (Anime)
                                                    @endif
                                                    {{ $exists ? ' - već dodano' : '' }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Nemaš anime/miješane kolekcije</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    @if($userCollections->count() > 0)
                                        <button type="submit" class="btn btn-outline-light btn-sm">
                                            <i class="bi bi-plus-circle me-2"></i>Dodaj u kolekciju
                                        </button>
                                    @else
                                        <p style="color: #e6e3e8; font-size: 0.9rem;">
                                            Nemaš anime ili miješane kolekcije.
                                        </p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Stvori novu kolekciju -->
                    <div class="col-md-6 mb-4">
                        <div class="card" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 10px;">
                            <div class="card-body">
                                <h6 style="color: #f5dad3;">Stvori novu kolekciju</h6>
                                <form method="POST" action="{{ route('collections.create-and-add') }}">
                                    @csrf
                                    <input type="hidden" name="anime_mal_id" value="{{ $anime['mal_id'] }}">
                                    <input type="hidden" name="anime_title" value="{{ $anime['title_english'] }}">
                                    
                                    <div class="mb-3">
                                        <label for="collection_name" class="form-label" style="color: #f5dad3;">Naziv:</label>
                                        <input type="text" class="form-control form-control-sm" id="collection_name" name="name" required
                                               style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;"
                                               placeholder="Moja nova kolekcija">
                                    </div>

                                    <div class="mb-3">
                                        <label for="collection_type" class="form-label" style="color: #f5dad3;">Tip:</label>
                                        <select class="form-select form-select-sm" id="collection_type" name="type" required
                                                style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                                            <option value="anime" selected>🎬 Anime</option>
                                            <option value="mixed">🎯 Miješano</option>
                                        </select>
                                        <small style="color: rgba(230, 227, 232, 0.7);">
                                            Manga tip nije dostupan jer dodaješ anime
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="collection_description" class="form-label" style="color: #f5dad3;">Opis:</label>
                                        <textarea class="form-control form-control-sm" id="collection_description" name="description" rows="2"
                                                  style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;"
                                                  placeholder="Kratki opis..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-outline-light btn-sm">
                                        <i class="bi bi-collection me-2"></i>Stvori i dodaj
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Zatvori</button>
            </div>
        </div>
    </div>
</div>
@endauth

@endsection