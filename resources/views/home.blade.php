@extends('layouts.app')

@section('title', 'Anime tracker')

@section('content')
<style>
    .anime-section {
        width: 90%;
        margin: 0 auto;
        transform: translateZ(0);
        will-change: transform;
        backface-visibility: hidden;
    }
    .anime-card {
        width: 200px;
        flex-shrink: 0;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        contain: content;
        content-visibility: auto;
    }
    .anime-image {
        width: auto;
        height: 300px;
        object-fit: contain;
    }
    .image-anime {
        width: 90%;
        align-self: center;
    }
    @media (max-width: 768px) {
        .anime-section {
            width: 100%;
        }
        .anime-card {
            width: 150px;
        }
        .anime-image {
            height: 225px;
        }
        .image-anime {
        width: 100%;
        align-self: center;
    }
    }
    .content-toggle {
        background: rgba(74, 38, 67, 0.4);
        border-radius: 26px;
        padding: 5px;
        display: inline-flex;
        margin-bottom: 20px;
    }
    .content-toggle .btn {
        border-radius: 20px;
        color: #e6e3e8;
        border: none;
        padding: 8px 20px;
    }
    .content-toggle .btn.active {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="mb-3 mt-5" data-bs-smooth-scroll="true">
    <div class="d-flex flex-column justify-content-center">
        @guest
        <div class="text-center align-middle mt-3 p-3 image-anime w-50" 
            style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 26px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); width: 30% !important;">
            <h2>Dobrodošli u sustav anime svijeta!</h2>
            <p>Prijavite se kako bi ste dodali u vlastitu kolekciju</p>
        </div>
        @else
        <div style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 26px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.4); width: 30% !important;" class="d-flex flex-row p-0 m-0 justify-content-center image-anime w-30">
            <h2 class="p-3 m-0">Dobrodošao<span style="color: #f5dad3;"> {{ Auth::user()->name }}</span>
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                alt="Avatar" class="rounded-circle" 
                style="width: 50px; height: 50px; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.3);">
            </h2>
        </div>
        @endguest

        <div class="text-center align-middle mt-3  image-anime w-auto" 
            style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 26px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="w-100 d-flex flex-row justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <h5>Pretraži anime ili mangu.</h5>
                    <form class="d-flex ms-3 me-3" style="height:50px" id="navbarSearchForm" action="/search" method="GET" role="search">
                        <input class="form-control me-2" type="search" name="q" id="navbarSearchInput" placeholder="Pretraži..." aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div> 
                    <img src="image/pixel.gif" style="opacity: 80%; border-top-right-radius: 26px; border-bottom-right-radius: 26px;">
            </div>        
        </div>
    </div>
</div>

@endsection