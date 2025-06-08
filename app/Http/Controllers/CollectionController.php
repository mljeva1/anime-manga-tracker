<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Anime;
use App\Models\Manga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Auth::user()->collections()->withCount('items')->get();
        return view('collections.index', compact('collections'));
    }

    public function store(Request $request)
    {
        Collection::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Kolekcija kreirana!');
    }

    public function show($id)
    {
        $collection = Collection::where('user_id', Auth::id())
            ->with(['items' => function($query) {
                // Učitaj samo relevantne podatke ovisno o tipu item-a
                $query->with(['animes' => function($q) {
                    $q->orderBy('episode_number');
                }, 'manga']);
            }])
            ->findOrFail($id);
            
        return view('collections.show', compact('collection'));
    }

    public function addAnime(Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'mal_id' => 'required|integer',
            'title' => 'required|string'
        ]);

        // Provjeri da li anime već postoji u kolekciji
        $exists = Item::where('collection_id', $request->collection_id)
                     ->where('mal_id', $request->mal_id)
                     ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anime "' . $request->title . '" je već dodan u kolekciju.');
        }

        // Dohvati podatke o animeu iz API-ja
        $response = Http::get("https://api.jikan.moe/v4/anime/{$request->mal_id}");
        
        if ($response->failed()) {
            return redirect()->back()->with('error', 'Greška pri dohvaćanju podataka o animeu.');
        }

        $animeData = $response->json('data');
        $episodeCount = $animeData['episodes'] ?? 12; // default ako nema podataka

        // Stvori item
        $item = Item::create([
            'collection_id' => $request->collection_id,
            'mal_id' => $request->mal_id,
            'title' => $request->title,
            'type' => 'anime',
            'status' => 'planned'
        ]);

        // Dodaj epizode s imenima ako su dostupne
        $this->addEpisodesWithNames($item, $request->mal_id, $episodeCount);

        return redirect()->back()->with('success', 'Anime "' . $request->title . '" je uspješno dodan u kolekciju s ' . $episodeCount . ' epizoda!');
    }

    private function addEpisodesWithNames($item, $malId, $episodeCount)
    {
        // Pokušaj dohvatiti imena epizoda iz API-ja
        $episodeNames = $this->getEpisodeNames($malId);

        // Dodaj epizode
        for ($i = 1; $i <= $episodeCount; $i++) {
            Anime::create([
                'item_id' => $item->id,
                'mal_id' => $malId,
                'episode_number' => $i,
                'is_watched' => false,
                'title' => $episodeNames[$i] ?? "Episode {$i}"
            ]);
        }
    }

    private function getEpisodeNames($malId)
    {
        $episodeNames = [];
        
        // Dohvati epizode iz API-ja (možda trebam više stranica)
        for ($page = 1; $page <= 5; $page++) { // Provjeri do 5 stranica (125 epizoda)
            $response = Http::get("https://api.jikan.moe/v4/anime/{$malId}/episodes", ['page' => $page]);
            
            if ($response->successful()) {
                $episodesData = $response->json();
                $episodes = $episodesData['data'] ?? [];
                
                if (empty($episodes)) {
                    break; // Nema više epizoda
                }
                
                foreach ($episodes as $episode) {
                    if (isset($episode['mal_id']) && isset($episode['title'])) {
                        $episodeNames[$episode['mal_id']] = $episode['title'];
                    }
                }
                
                // Rate limit protection
                usleep(350000); // 0.35 sekundi između poziva
            } else {
                break;
            }
        }
        
        return $episodeNames;
    }

    public function updateEpisodeTitles($itemId)
    {
        $item = Item::findOrFail($itemId);
        $episodeNames = $this->getEpisodeNames($item->mal_id);
        
        foreach ($item->animes as $episode) {
            if (isset($episodeNames[$episode->episode_number])) {
                $episode->update(['title' => $episodeNames[$episode->episode_number]]);
            }
        }
        
        return redirect()->back()->with('success', 'Nazivi epizoda su ažurirani!');
    }

    public function toggleEpisode($itemId, $episodeNumber)
    {
        $anime = Anime::where('item_id', $itemId)
            ->where('episode_number', $episodeNumber)
            ->firstOrFail();
            
        $anime->update(['is_watched' => !$anime->is_watched]);
        
        return redirect()->back()->with('success', 'Episode ' . $episodeNumber . ' označena kao ' . ($anime->is_watched ? 'pogledana' : 'nepogledana'));
    }

    public function createAndAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:anime,manga,mixed',
            'description' => 'nullable|string',
            'anime_mal_id' => 'required|integer',
            'anime_title' => 'required|string'
        ]);

        // Stvori novu kolekciju
        $collection = Collection::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description
        ]);

        // Dohvati podatke o animeu iz API-ja
        $response = Http::get("https://api.jikan.moe/v4/anime/{$request->anime_mal_id}");
        
        if ($response->failed()) {
            return redirect()->back()->with('error', 'Greška pri dohvaćanju podataka o animeu.');
        }

        $animeData = $response->json('data');
        $episodeCount = $animeData['episodes'] ?? 12;

        // Stvori item u novoj kolekciji
        $item = Item::create([
            'collection_id' => $collection->id,
            'mal_id' => $request->anime_mal_id,
            'title' => $request->anime_title,
            'type' => 'anime',
            'status' => 'planned'
        ]);

        // Dodaj epizode
        $this->addEpisodesWithNames($item, $request->anime_mal_id, $episodeCount);

        return redirect()->back()->with('success', 'Kolekcija "' . $request->name . '" je kreirana i anime "' . $request->anime_title . '" je dodan s ' . $episodeCount . ' epizoda!');
    }

    public function addManga(Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'mal_id' => 'required|integer',
            'title' => 'required|string'
        ]);

        // Provjeri da li manga već postoji u kolekciji
        $exists = Item::where('collection_id', $request->collection_id)
                    ->where('mal_id', $request->mal_id)
                    ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Manga "' . $request->title . '" je već dodana u kolekciju.');
        }

        // Stvori item
        $item = Item::create([
            'collection_id' => $request->collection_id,
            'mal_id' => $request->mal_id,
            'title' => $request->title,
            'type' => 'manga',
            'status' => 'planned'
        ]);

        // Dodaj manga entry
        Manga::create([
            'item_id' => $item->id,
            'mal_id' => $request->mal_id,
            'is_read' => false
        ]);

        return redirect()->back()->with('success', 'Manga "' . $request->title . '" je uspješno dodana u kolekciju!');
    }

    public function createAndAddManga(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:anime,manga,mixed',
            'description' => 'nullable|string',
            'manga_mal_id' => 'required|integer',
            'manga_title' => 'required|string'
        ]);

        // Stvori novu kolekciju
        $collection = Collection::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description
        ]);

        // Stvori item u novoj kolekciji
        $item = Item::create([
            'collection_id' => $collection->id,
            'mal_id' => $request->manga_mal_id,
            'title' => $request->manga_title,
            'type' => 'manga',
            'status' => 'planned'
        ]);

        // Dodaj manga entry
        Manga::create([
            'item_id' => $item->id,
            'mal_id' => $request->manga_mal_id,
            'is_read' => false
        ]);

        return redirect()->back()->with('success', 'Kolekcija "' . $request->name . '" je kreirana i manga "' . $request->manga_title . '" je dodana!');
    }

    public function toggleMangaRead($itemId)
    {
        $manga = Manga::where('item_id', $itemId)->firstOrFail();
        $manga->update(['is_read' => !$manga->is_read]);
        
        return redirect()->back()->with('success', 'Manga označena kao ' . ($manga->is_read ? 'pročitana' : 'nepročitana'));
    }

}