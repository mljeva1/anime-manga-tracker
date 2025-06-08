<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MangaController extends Controller // Veliko M!
{
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
        $response = Http::get('https://api.jikan.moe/v4/manga', $apiParams);
        
        $manga = [];
        $pagination = null;
        
        if ($response->successful()) {
            $data = $response->json();
            $manga = $data['data'] ?? [];
            $pagination = $data['pagination'] ?? null;
        }
        
        return view('manga.index', compact('manga', 'pagination', 'page', 'orderBy', 'sort', 'startDate', 'endDate', 'status'));
    }

    public function show(Request $request, $mal_id) {
        $mangaResponse = Http::get("https://api.jikan.moe/v4/manga/{$mal_id}/full");
        
        if ($mangaResponse->failed()) {
            abort(404, 'Manga not found');
        }

        $manga = $mangaResponse->json('data');

        if (empty($manga['title_english'])) {
            abort(404, 'Manga details not available in English');
        }
        
        $chapters = $manga['chapters'] ?? null;
        $volumes = $manga['volumes'] ?? null;
        $status = $manga['status'] ?? 'Unknown';
        
        // Dohvati reviews
        $reviewsResponse = Http::get("https://api.jikan.moe/v4/manga/{$mal_id}/reviews", [
            'page' => 1
        ]);
        
        $reviews = [];
        if ($reviewsResponse->successful()) {
            $reviewsData = $reviewsResponse->json();
            $reviews = $reviewsData['data'] ?? [];
            
            if (!is_array($reviews)) {
                $reviews = [];
            }
        }
        
        return view('manga.show', compact('manga', 'reviews', 'chapters', 'volumes', 'status'));
    }
}