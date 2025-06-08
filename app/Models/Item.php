<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'mal_id',
        'title',
        'type',
        'status'
    ];

    /**
     * Dohvaća kolekciju kojoj pripada stavka.
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Dohvaća anime povezan sa stavkom.
     */
    public function animes()
    {
        return $this->hasMany(Anime::class);
    }

    /**
     * Dohvaća mangu povezanu sa stavkom.
     */
    public function manga()
    {
        return $this->hasOne(Manga::class);
    }
    /**
     * Dohvaća epizode animea ako je tip stavke 'anime'.
     */
    public function animeEpisodes()
    {
        if ($this->type === 'anime') {
            return $this->hasMany(Anime::class);
        }
        return null;
    }

    /**
     * Dohvaća poglavlja mange ako je tip stavke 'manga'.
     */
    public function mangaChapters()
    {
        if ($this->type === 'manga') {
            return $this->hasMany(Manga::class);
        }
        return null;
    }
}