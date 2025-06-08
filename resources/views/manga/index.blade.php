@extends('layouts.app')

@section('title', 'Manga Lista')

@section('content')
<style>
.filter-container {
    background: rgba(74, 38, 67, 0.4);
    border-radius: 15px;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #e6e3e8;
}

.manga-card {
    background: rgba(74, 38, 67, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    color: #e6e3e8;
    height: 100%;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
}

.manga-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.manga-poster {
    height: 300px !important;
    width: 100% !important;
    object-fit: cover !important;
    border-radius: 15px 15px 0 0 !important;
}

.card-body {
    display: flex;
    flex-direction: column;
    flex: 1;
}

option {
    background: rgba(74, 38, 67, 0.9);
}
</style>

<div class="container-fluid my-5">
    <!-- Naslov -->
    <div class="row justify-content-center mb-4">
        <div class="col-auto">
            <h1 class="ps-3 pe-3 pt-2 pb-2"
                style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 26px;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.2);">
                <i class="bi bi-book me-2"></i>Manga Lista
            </h1>
        </div>
    </div>

    <!-- Filter i Sortiranje -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="filter-container p-3">
                <form method="GET" action="{{ route('manga.index') }}">
                    <div class="row g-3">

                        <!-- Sortiranje -->
                        <div class="col-md-3">
                            <label class="form-label" style="color: #f5dad3;">Sortiraj po:</label>
                            <select name="order_by" class="form-select form-select-sm" 
                                    style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                                <option value="popularity" {{ ($orderBy ?? 'popularity') === 'popularity' ? 'selected' : '' }}>Popularnost</option>
                                <option value="score" {{ ($orderBy ?? '') === 'score' ? 'selected' : '' }}>Ocjena</option>
                                <option value="rank" {{ ($orderBy ?? '') === 'rank' ? 'selected' : '' }}>Rang</option>
                                <option value="title" {{ ($orderBy ?? '') === 'title' ? 'selected' : '' }}>Naziv</option>
                                <option value="start_date" {{ ($orderBy ?? '') === 'start_date' ? 'selected' : '' }}>Datum početka</option>
                                <option value="chapters" {{ ($orderBy ?? '') === 'chapters' ? 'selected' : '' }}>Broj poglavlja</option>
                                <option value="members" {{ ($orderBy ?? '') === 'members' ? 'selected' : '' }}>Članovi</option>
                                <option value="favorites" {{ ($orderBy ?? '') === 'favorites' ? 'selected' : '' }}>Omiljeni</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label class="form-label" style="color: #f5dad3;">Status:</label>
                            <select name="status" class="form-select form-select-sm" 
                                    style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                                <option value="">Svi</option>
                                <option value="publishing" {{ ($status ?? '') === 'publishing' ? 'selected' : '' }}>Publishing</option>
                                <option value="complete" {{ ($status ?? '') === 'complete' ? 'selected' : '' }}>Complete</option>
                                <option value="hiatus" {{ ($status ?? '') === 'hiatus' ? 'selected' : '' }}>Hiatus</option>
                                <option value="discontinued" {{ ($status ?? '') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                <option value="upcoming" {{ ($status ?? '') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            </select>
                        </div>

                        <!-- Smjer sortiranja -->
                        <div class="col-md-2">
                            <label class="form-label" style="color: #f5dad3;">Redoslijed:</label>
                            <select name="sort" class="form-select form-select-sm"
                                    style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                                <option value="asc" {{ ($sort ?? 'asc') === 'asc' ? 'selected' : '' }}>Uzlazno</option>
                                <option value="desc" {{ ($sort ?? '') === 'desc' ? 'selected' : '' }}>Silazno</option>
                            </select>
                        </div>

                        <!-- Datum početka -->
                        <div class="col-md-2">
                            <label class="form-label" style="color: #f5dad3;">Od datuma:</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" 
                                   value="{{ $startDate ?? '' }}"
                                   style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                        </div>

                        <!-- Datum kraja -->
                        <div class="col-md-2">
                            <label class="form-label" style="color: #f5dad3;">Do datuma:</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" 
                                   value="{{ $endDate ?? '' }}"
                                   style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); color: #e6e3e8;">
                        </div>

                        <!-- Gumbovi -->
                        <div class="col-md-1">
                            <label class="form-label" style="opacity: 0;">Gumb</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-funnel"></i>
                                </button>
                                <a href="{{ route('manga.index') }}" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manga Grid -->
    @if(!empty($manga))
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row">
                @foreach($manga as $item)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="manga-card">
                            @if($item['images']['webp']['image_url'] ?? null)
                                <img src="{{ $item['images']['webp']['image_url'] }}" 
                                     class="manga-poster"
                                     alt="{{ $item['title_english'] ?? $item['title'] }}">
                            @endif
                            <div class="card-body p-3">
                                <h6 class="card-title mb-2" style="color: #f5dad3; font-size: 0.9rem; line-height: 1.2;">
                                    {{ \Illuminate\Support\Str::limit($item['title_english'] ?? $item['title'], 40) }}
                                </h6>
                                
                                <div class="mb-2">
                                    <span class="badge me-1" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3; font-size: 0.7rem;">
                                        {{ $item['type'] }}
                                    </span>
                                    @if($item['score'])
                                        <span class="badge" style="background: rgba(74, 38, 67, 0.8); color: #f5dad3; font-size: 0.7rem;">
                                            <i class="bi bi-star-fill"></i> {{ $item['score'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-2">
                                    <small style="color: #e6e3e8; font-size: 0.75rem;">
                                        @if($item['chapters'])
                                            {{ $item['chapters'] }} ch
                                        @endif
                                        @if($item['volumes'])
                                            • {{ $item['volumes'] }} vol
                                        @endif
                                        @if($item['published']['prop']['from']['year'] ?? null)
                                            • {{ $item['published']['prop']['from']['year'] }}
                                        @endif
                                    </small>
                                </div>

                                <div class="mt-auto">
                                    @if($item['title_english'])
                                        <a href="{{ route('manga.show', $item['mal_id']) }}" 
                                           class="btn btn-outline-light btn-sm w-100">
                                            <i class="bi bi-eye me-1"></i>Detalji
                                        </a>
                                    @else
                                        <small style="color: #f5dad3 !important;">Nema engleskog naziva</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Paginacija -->
    @if($pagination && ($pagination['last_visible_page'] ?? 1) > 1)
    <nav class="mt-5" aria-label="Manga pagination">
        <ul class="pagination justify-content-center">
            <!-- Previous -->
            @if(($pagination['has_previous_page'] ?? false) || ($page ?? 1) > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => ($page ?? 1) - 1]) }}"
                       style="background: rgba(74, 38, 67, 0.6); border-color: rgba(255, 255, 255, 0.2); color: #f5dad3;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            <!-- Page numbers -->
            @php
                $totalPages = $pagination['last_visible_page'] ?? 1;
                $currentPage = $page ?? 1;
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
            @endphp

            @for($i = $start; $i <= $end; $i++)
                @if($i == $currentPage)
                    <li class="page-item active">
                        <span class="page-link" style="background: rgba(74, 38, 67, 0.9); border-color: #f5dad3; color: #f5dad3;">
                            {{ $i }}
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                           style="background: rgba(74, 38, 67, 0.6); border-color: rgba(255, 255, 255, 0.2); color: #f5dad3;">
                            {{ $i }}
                        </a>
                    </li>
                @endif
            @endfor

            <!-- Next -->
            @if(($pagination['has_next_page'] ?? false) || ($currentPage < $totalPages))
                <li class="page-item">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => ($page ?? 1) + 1]) }}"
                       style="background: rgba(74, 38, 67, 0.6); border-color: rgba(255, 255, 255, 0.2); color: #f5dad3;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <!-- Info o stranicama -->
    <div class="text-center mt-3">
        <small style="color: #f5dad3;">
            Stranica {{ $page ?? 1 }} od {{ $pagination['last_visible_page'] ?? 1 }}
            ({{ count($manga) }} manga na ovoj stranici)
        </small>
    </div>
    @endif

    @else
        <div class="text-center py-5">
            <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: rgba(255, 255, 255, 0.3);"></i>
            <h3 class="mt-3" style="color: #f5dad3;">Nema manga za prikaz</h3>
            <p style="color: #e6e3e8;">Pokušajte promijeniti filtere.</p>
        </div>
    @endif
</div>




@endsection