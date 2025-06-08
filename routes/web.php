<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('home');
});
Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/login', function() {
    return view('auth.login');
})->name('login');
Route::controller(UserController::class)->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('login.post');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'register']);
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/random-avatars', [UserController::class, 'getRandomAvatars']);
    Route::put('/update-avatar', [UserController::class, 'updateAvatar'])->name('user.update-avatar');
    Route::get('/regenerate-avatars', [UserController::class, 'regenerateAvatars'])->name('user.regenerate-avatars');
});
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');

Route::get('/anime', [AnimeController::class, 'index'])->name('anime.index');
Route::get('/anime/{mal_id}', [App\Http\Controllers\AnimeController::class, 'show'])->name('anime.show');
Route::get('/anime/{mal_id}/reviews', [App\Http\Controllers\AnimeController::class, 'reviews'])->name('anime.reviews');

Route::get('/manga', [App\Http\Controllers\MangaController::class, 'index'])->name('manga.index');
Route::get('/manga/{mal_id}', [App\Http\Controllers\MangaController::class, 'show'])->name('manga.show');
Route::get('/manga/{mal_id}/reviews', [App\Http\Controllers\MangaController::class, 'reviews'])->name('manga.reviews');

Route::middleware('auth')->group(function () {
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/collections/{id}', [CollectionController::class, 'show'])->name('collections.show');
    Route::post('/collections/add-anime', [CollectionController::class, 'addAnime'])->name('collections.add-anime');
    Route::post('/collections/create-and-add', [CollectionController::class, 'createAndAdd'])->name('collections.create-and-add');
    Route::post('/collections/toggle/{item}/{episode}', [CollectionController::class, 'toggleEpisode'])->name('collections.toggle');

    Route::post('/collections/add-manga', [CollectionController::class, 'addManga'])->name('collections.add-manga');
    Route::post('/collections/create-and-add-manga', [CollectionController::class, 'createAndAddManga'])->name('collections.create-and-add-manga');
    Route::post('/collections/toggle-manga/{item}', [CollectionController::class, 'toggleMangaRead'])->name('collections.toggle-manga');
});