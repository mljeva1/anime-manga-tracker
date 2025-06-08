@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center mt-3">
    @auth
        <div style="color:#e6e3e8 !important;
            background: rgba(74, 38, 67, 0.4);
            border-radius: 26px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);"
            class="d-flex flex-column p-3">
            
            <div class="d-flex align-items-center pb-2">
                <div class="avatar-container me-3">
                    <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                        alt="Avatar" 
                        class="rounded-circle" 
                        style="width: 60px; height: 60px; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.3);">
                </div>
                <h1>Korisnički podaci</h1>
            </div>
            
            <!-- Gumb za otvaranje modala -->
            <div class="mb-3 text-center">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#avatarModal"
                style="background: rgba(74, 38, 67, 0.8); border: none; border-radius: 23px; padding: 10px 30px;">
                    Promijeni sliku profila
                </button>
            </div>
    
            <form action="{{ route('user.update', Auth::user()->id) }}" method="POST">
                @csrf
                @method('PUT')        
                <div class="mb-3">
                    <label for="role{{ Auth::user()->id }}" class="form-label p-0 m-0"><h4>Rola</h4></label>
                    <input type="text" class="form-control text-center fs-5" id="role{{ Auth::user()->id }}" name="role" value="{{ Auth::user()->role->name ?? 'Nije dodijeljena' }}" disabled
                    style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 23px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
                </div>                          
                <div class="mb-3">
                    <label for="name{{ Auth::user()->id }}" class="form-label p-0 m-0"><h4>Username</h4></label>
                    <input type="text" class="form-control text-center fs-5" id="name{{ Auth::user()->id }}" name="name" value="{{ Auth::user()->name }}" placeholder="{{ Auth::user()->name }}"
                    style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 23px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
                </div>
                <div class="mb-3">
                    <label for="first_name{{ Auth::user()->id }}" class="form-label p-0 m-0"><h4>Ime</h4></label>
                    <input type="text" class="form-control text-center fs-5" id="first_name{{ Auth::user()->id }}" name="first_name" value="{{ Auth::user()->first_name }}" placeholder="{{ Auth::user()->first_name }}"
                    style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 23px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
                </div>
                <div class="mb-3">
                    <label for="last_name{{ Auth::user()->id }}" class="form-label p-0 m-0"><h4>Prezime</h4></label>
                    <input type="text" class="form-control text-center fs-5" id="last_name{{ Auth::user()->id }}" name="last_name" value="{{ Auth::user()->last_name }}" placeholder="{{ Auth::user()->last_name }}"
                    style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 23px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
                </div>
                <div class="mb-3">
                    <label for="email{{ Auth::user()->id }}" class="form-label p-0 m-0"><h4>Email (po defaultu prazno)</h4></label>
                    <input type="text" class="form-control text-center fs-5" id="email{{ Auth::user()->id }}" name="email" value="{{ Auth::user()->email }}" placeholder="{{ Auth::user()->email }}"
                    style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 23px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
                </div>
                <div class="mb-3">
                    <label for="password{{ Auth::user()->id }}" class="form-label p-0 m-0"><h4>Nova lozinka (ostavite prazno ako ne želite mijenjati)</h4></label>
                    <input type="password" class="form-control text-center fs-5" id="password{{ Auth::user()->id }}" name="password" 
                    style="color:#e6e3e8 !important; background: rgba(74, 38, 67, 0.4); border-radius: 23px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary" style="background: rgba(74, 38, 67, 0.8); border: none; border-radius: 23px; padding: 10px 30px;">
                        Spremi promjene
                    </button>
                </div>
            </form>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    
        <!-- Modal za odabir avatara -->
        <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="background: rgba(74, 38, 67, 0.9); color: #e6e3e8;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avatarModalLabel">Odaberi novi avatar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <button id="regenerateAvatars" class="btn btn-primary" style="background: rgba(135, 102, 150, 0.8); border: none; border-radius: 23px; padding: 10px 30px;">
                                <i class="bi bi-arrow-repeat"></i> Regeneriraj avatare
                            </button>
                        </div>
                        
                        <div id="avatarsContainer" class="row">
                            @foreach($randomAvatars as $avatar)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100" style="background: rgba(135, 102, 150, 0.4); border: none; cursor: pointer;">
                                        <form action="{{ route('user.update-avatar') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="avatar" value="{{ $avatar['image'] }}">
                                            <button type="submit" style="padding: 0; border: none; background: none; width: 100%;">
                                                <img src="{{ $avatar['image'] }}" class="card-img-top" alt="{{ $avatar['name'] }}" 
                                                     style="height: 200px; object-fit: cover;">
                                            </button>
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ $avatar['name'] }}</h6>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth
</div>

<!-- Minimalni JavaScript za regeneriranje avatara bez osvježavanja stranice -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gumb za regeneriranje avatara
    const regenerateBtn = document.getElementById('regenerateAvatars');
    regenerateBtn.addEventListener('click', function() {
        const avatarsContainer = document.getElementById('avatarsContainer');
        avatarsContainer.innerHTML = '<div class="text-center w-100"><div class="spinner-border text-light" role="status"></div></div>';
        
        fetch('/regenerate-avatars')
            .then(response => response.json())
            .then(avatars => {
                let html = '';
                
                avatars.forEach(avatar => {
                    html += `
                        <div class="col-md-4 mb-3">
                            <div class="card h-100" style="background: rgba(135, 102, 150, 0.4); border: none; cursor: pointer;">
                                <form action="{{ route('user.update-avatar') }}" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="avatar" value="${avatar.image}">
                                    <button type="submit" style="padding: 0; border: none; background: none; width: 100%;">
                                        <img src="${avatar.image}" class="card-img-top" alt="${avatar.name}" 
                                             style="height: 200px; object-fit: cover;">
                                    </button>
                                    <div class="card-body text-center">
                                        <h6 class="card-title">${avatar.name}</h6>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `;
                });
                
                avatarsContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Greška:', error);
                avatarsContainer.innerHTML = '<div class="alert alert-danger">Greška pri dohvatu avatara</div>';
            });
    });
});
</script>
@endsection
