@extends('layouts.app')

@section('title', $manga['title_english'])

@section('content')
<style>
.detail-container {
    background: rgba(74, 38, 67, 0.6);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #e6e3e8;
}

.manga-poster {
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
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-container p-4">
                <div class="row">
                    <!-- Poster -->
                    <div class="col-md-4 text-center mb-4">
                        <img src="{{ $manga['images']['webp']['large_image_url'] ?? $manga['images']['webp']['image_url'] }}" 
                             alt="{{ $manga['title_english'] }}" class="manga-poster">
                    </div>
                    
                    <!-- Informacije -->
                    <div class="col-md-8">
                        <h1 class="mb-3" style="color: #f5dad3;">{{ $manga['title_english'] }}</h1>
                        @if($manga['title_japanese'])
                            <h2 class="mb-3" style="color: #f5dad3; font-size: 1.2rem;">{{ $manga['title_japanese'] }}</h2>
                        @endif
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-item">
                                    <span class="info-label">Type:</span> {{ $manga['type'] }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Chapters:</span> {{ $chapters ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Volumes:</span> {{ $volumes ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Status:</span> {{ $status }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Published:</span> {{ $manga['published']['string'] ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-item">
                                    <span class="info-label">Score:</span> 
                                    @if($manga['score'])
                                        <span class="badge" style="background: rgba(74, 38, 67, 0.6);">{{ $manga['score'] }}/10</span>
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Rank:</span> #{{ $manga['rank'] ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Popularity:</span> #{{ $manga['popularity'] ?? 'N/A' }}
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Members:</span> {{ number_format($manga['members'] ?? 0) }}
                                </div>
                                <!-- Reviews gumb -->
                                <button type="button" class="btn btn-outline-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#reviewsModal">
                                    <i class="bi bi-chat-square-text me-2"></i>Reviews
                                </button>
                            </div>
                        </div>

                        <!-- Žanrovi -->
                        @if($manga['genres'])
                        <div class="info-item">
                            <span class="info-label">Genres:</span>
                            @foreach($manga['genres'] as $genre)
                                <span class="badge me-1" style="background: rgba(74, 38, 67, 0.6);">{{ $genre['name'] }}</span>
                            @endforeach
                        </div>
                        @endif

                        <!-- Authors -->
                        @if($manga['authors'])
                        <div class="info-item">
                            <span class="info-label">Authors:</span>
                            @foreach($manga['authors'] as $author)
                                <span class="badge me-1" style="background: rgba(74, 38, 67, 0.6);">{{ $author['name'] }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Synopsis -->
                @if($manga['synopsis'])
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="info-label">Synopsis</h3>
                        <p style="line-height: 1.6;">{{ $manga['synopsis'] }}</p>
                    </div>
                </div>
                @endif

                @if($manga['background'])
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="info-label">Background</h3>
                        <p style="line-height: 1.6;">{{ $manga['background'] }}</p>
                    </div>
                </div>
                @endif

                <!-- Gumbovi -->
                <div class="row mt-4">
                    <div class="col-12 text-center">                
                        @auth
                            <button type="button" class="btn btn-outline-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="bi bi-plus-square me-2"></i>Dodaj u kolekciju
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

<!-- Reviews Modal (isti kao za anime) -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="background: rgba(74, 38, 67, 0.9); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 15px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                <h5 class="modal-title" id="reviewsModalLabel" style="color: #f5dad3;">
                    <i class="bi bi-chat-square-text me-2"></i>Reviews - {{ $manga['title_english'] }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body" style="color: #e6e3e8; max-height: 70vh; overflow-y: auto;">
                @if(!empty($reviews))
                    @foreach($reviews as $review)
                        <!-- Isti review HTML kao za anime -->
                        <div class="review-item mb-4 p-3" style="background: rgba(255, 255, 255, 0.1); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.2);">
                            <!-- Review sadržaj isti kao kod anime -->
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    @if($review['user']['images']['jpg']['image_url'] ?? null)
                                        <img src="{{ $review['user']['images']['jpg']['image_url'] }}" 
                                             alt="{{ $review['user']['username'] }}" 
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
                                        {{ date('d.m.Y', strtotime($review['date'])) }}
                                    </small>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-2">
                                        @if($review['score'])
                                            <span class="badge" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3;">
                                                <i class="bi bi-star-fill me-1"></i>{{ $review['score'] }}/10
                                            </span>
                                        @endif
                                    </div>
                                    <div class="review-text">
                                        <p style="color: #f5dad3; line-height: 1.5; font-size: 0.9rem;">
                                            {{ \Illuminate\Support\Str::limit($review['review'] ?? 'Nema teksta recenzije.', 600) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 1.5rem 0;">
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-chat-square-text" style="font-size: 3rem; color: rgba(255, 255, 255, 0.3);"></i>
                        <h5 class="mt-3" style="color: #f5dad3;">Nema dostupnih recenzija</h5>
                        <p style="color: #e6e3e8;">Za ovu mangu trenutno nema recenzija.</p>
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
                    <i class="bi bi-plus-square me-2"></i>Dodaj manga u kolekciju
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
                                <form method="POST" action="{{ route('collections.add-manga') }}">
                                    @csrf
                                    <input type="hidden" name="mal_id" value="{{ $manga['mal_id'] }}">
                                    <input type="hidden" name="title" value="{{ $manga['title_english'] }}">
                                    
                                    <div class="mb-3">
                                        <label for="collection_id" class="form-label" style="color: #f5dad3;">Odaberi kolekciju:</label>
                                        <select class="form-select form-select-sm" id="collection_id" name="collection_id" required
                                                style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                                            <option value="">Odaberi kolekciju</option>
                                            @php
                                                // FILTRIRANJE: Prikaži samo manga i mixed kolekcije
                                                $userCollections = Auth::user()->collections()
                                                    ->whereIn('type', ['manga', 'mixed'])
                                                    ->get();
                                            @endphp
                                            @forelse($userCollections as $collection)
                                                @php
                                                    $exists = $collection->items()->where('mal_id', $manga['mal_id'])->exists();
                                                @endphp
                                                <option value="{{ $collection->id }}" {{ $exists ? 'disabled' : '' }}>
                                                    {{ $collection->name }}
                                                    @if($collection->type === 'mixed')
                                                        (Miješano)
                                                    @else
                                                        (Manga)
                                                    @endif
                                                    {{ $exists ? ' - već dodano' : '' }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Nemaš manga/miješane kolekcije</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    @if($userCollections->count() > 0)
                                        <button type="submit" class="btn btn-outline-light btn-sm">
                                            <i class="bi bi-plus-circle me-2"></i>Dodaj u kolekciju
                                        </button>
                                    @else
                                        <p style="color: #e6e3e8; font-size: 0.9rem;">
                                            Nemaš manga ili miješane kolekcije.
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
                                <form method="POST" action="{{ route('collections.create-and-add-manga') }}">
                                    @csrf
                                    <input type="hidden" name="manga_mal_id" value="{{ $manga['mal_id'] }}">
                                    <input type="hidden" name="manga_title" value="{{ $manga['title_english'] }}">
                                    
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
                                            <option value="manga" selected>📚 Manga</option>
                                            <option value="mixed">🎯 Miješano</option>
                                        </select>
                                        <small style="color: rgba(230, 227, 232, 0.7);">
                                            Anime tip nije dostupan jer dodaješ manga
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