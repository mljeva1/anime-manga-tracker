<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class JikanService
{
    protected $baseUrl = 'https://api.jikan.moe/v4';

    public function getRandomCharacter()
    {
        // Generiramo jedinstveni ključ za cache
        $cacheKey = 'random_character_' . md5(time() . rand(1, 1000));
        
        // Cachiramo rezultat na 1 sat
        return Cache::remember($cacheKey, 3600, function() {
            try {
                // Dodajemo delay za poštivanje rate limita
                usleep(250000); // 0.25 sekundi
                
                $response = Http::get($this->baseUrl . '/random/characters');
                
                if ($response->successful()) {
                    return $response->json()['data'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('Jikan API greška: ' . $e->getMessage());
                return null;
            }
        });
    }
}
