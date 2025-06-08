<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AnimeController extends Controller
{
    private function JikanAPI() {
        $response = Http::get('https://api.jikan.moe/v4/random/anime');
    }

    public function index(Request $request)
    {
        // Parametri za API
        $page = $request->input('page', 1);
        $orderBy = $request->input('order_by', 'popularity');
        $sort = $request->input('sort', 'asc');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status'); 

         // API parametri
        $apiParams = [
            'page' => $page,
            'limit' => 24, // 24 po stranici
            'order_by' => $orderBy,
            'sort' => $sort,
            'status' => $status
        ];
        
        // Dodaj datume ako su postavljeni
        if ($startDate) {
            $apiParams['start_date'] = $startDate;
        }
        if ($endDate) {
            $apiParams['end_date'] = $endDate;
        }
        if ($status) {
            $apiParams['status'] = $status;
        }

        // API poziv
        $response = Http::get('https://api.jikan.moe/v4/anime', $apiParams);
        
        $anime = [];
        $pagination = null;
        
        if ($response->successful()) {
            $data = $response->json();
            $anime = $data['data'] ?? [];
            $pagination = $data['pagination'] ?? null;
        }
        
        return view('anime.index', compact('anime', 'pagination', 'page', 'orderBy', 'sort', 'startDate', 'endDate', 'status'));
    }


    public function show(Request $request, $mal_id) {
        // Dohvati osnovne informacije o animeu
        $animeResponse = Http::get("https://api.jikan.moe/v4/anime/{$mal_id}/full");

        if ($animeResponse->failed()) {
            abort(404, 'Anime not found');
        }

        $anime = $animeResponse->json('data');

        if (empty($anime['title_english'])) {
            abort(404, 'Anime details not available in English');
        }

        // Trenutna stranica korisnika
        $currentPage = $request->input('episodes_page', 1);
        $episodesPerPage = 25;
        
        // Cache ključ za sve epizode ovog animea
        $cacheKey = "anime_episodes_{$mal_id}";
        
        // Dohvati sve epizode iz cache-a ili API-ja
        $allEpisodes = Cache::remember($cacheKey, 3600, function() use ($mal_id) {
            $allEpisodes = [];
            $apiPage = 1;
            
            do {
                $episodesResponse = Http::get("https://api.jikan.moe/v4/anime/{$mal_id}/episodes", [
                    'page' => $apiPage
                ]);
                
                if ($episodesResponse->successful()) {
                    $episodesData = $episodesResponse->json();
                    $pageEpisodes = $episodesData['data'] ?? [];
                    
                    // DODAJ PROVJERU DA SU EPIZODE VALIDNE
                    if (!empty($pageEpisodes) && is_array($pageEpisodes)) {
                        // Provjeri da svaki element ima potrebne ključeve
                        $validEpisodes = array_filter($pageEpisodes, function($episode) {
                            return is_array($episode) && isset($episode['mal_id']);
                        });
                        
                        if (!empty($validEpisodes)) {
                            $allEpisodes = array_merge($allEpisodes, $validEpisodes);
                            $apiPage++;
                        } else {
                            break;
                        }
                    } else {
                        break;
                    }
                    
                    if ($apiPage > 1) {
                        usleep(350000);
                    }
                    if ($apiPage > 100) break;
                } else {
                    break;
                }
            } while (!empty($pageEpisodes));
            
            return $allEpisodes;
        });
        
        // DODAJ SIGURNOSNU PROVJERU
        if (!is_array($allEpisodes)) {
            $allEpisodes = [];
        }
        
        // Filtriraj ponovno da budeš siguran
        $allEpisodes = array_filter($allEpisodes, function($episode) {
            return is_array($episode) && isset($episode['mal_id']);
        });
        
        // Paginacija
        $totalEpisodes = count($allEpisodes);
        $offset = ($currentPage - 1) * $episodesPerPage;
        $episodes = array_slice($allEpisodes, $offset, $episodesPerPage);
        
        // Izračunaj ukupan broj stranica
        $totalPages = ceil($totalEpisodes / $episodesPerPage);
        
        // Kreiraj paginaciju
        $customPagination = [
            'current_page' => $currentPage,
            'last_visible_page' => $totalPages,
            'has_next_page' => $currentPage < $totalPages,
            'items' => [
                'total' => $totalEpisodes
            ]
        ];
        
        // Dohvati reviews
        $reviewsResponse = Http::get("https://api.jikan.moe/v4/anime/{$mal_id}/reviews", [
            'page' => 1
        ]);
        
        $reviews = [];
        if ($reviewsResponse->successful()) {
            $reviewsData = $reviewsResponse->json();
            $reviews = $reviewsData['data'] ?? [];
            
            // PROVJERI I REVIEWS
            if (!is_array($reviews)) {
                $reviews = [];
            }
        }
        
        return view('anime.show', compact('anime', 'episodes', 'customPagination', 'currentPage', 'totalEpisodes', 'reviews'));
    }

}