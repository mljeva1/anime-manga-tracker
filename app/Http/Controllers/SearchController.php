<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'anime'); // default: anime
        $results = collect();

        if ($query) {
            if ($type === 'anime') {
                // Dohvati samo anime
                $data = Http::get('https://api.jikan.moe/v4/anime', [
                    'q' => $query
                ])->json('data') ?? [];
            } else {
                // Dohvati samo manga
                $data = Http::get('https://api.jikan.moe/v4/manga', [
                    'q' => $query
                ])->json('data') ?? [];
            }

            $results = collect($data)
                ->filter(fn($item) => !empty($item['title_english']))
                ->take(25)
                ->map(fn($item) => [
                    'mal_id' => $item['mal_id'],
                    'title' => $item['title_english'],
                    'url'   => $item['url'],
                    'image' => $item['images']['webp']['large_image_url'] ?? null,
                    'type'  => $item['type'],
                    'synopsis' => $item['synopsis'] ?? '',
                ]);
        }

        return view('search', [
            'results' => $results,
            'query' => $query,
            'currentType' => $type,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
