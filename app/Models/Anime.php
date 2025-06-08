<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'mal_id',
        'episode_number',
        'title',
        'is_watched'
    ];

    /**
     * Atributi koji trebaju biti pretvoreni.
     *
     * @var array
     */
    protected $casts = [
        'is_watched' => 'boolean',
    ];

    /**
     * Dohvaća stavku kojoj pripada anime.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}