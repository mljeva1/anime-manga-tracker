<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'description',
        'is_finished',
        'default'
    ];

    /**
     * Atributi koji trebaju biti pretvoreni.
     *
     * @var array
     */
    protected $casts = [
        'is_finished' => 'boolean',
        'default' => 'boolean',
    ];

    /**
     * Dohvaća korisnika kojem pripada kolekcija.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dohvaća stavke u kolekciji.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Dohvaća animee u kolekciji.
     */
    public function animes()
    {
        return $this->hasManyThrough(Anime::class, Item::class);
    }

    /**
     * Dohvaća mange u kolekciji.
     */
    public function mangas()
    {
        return $this->hasManyThrough(Manga::class, Item::class);
    }
}