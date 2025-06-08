<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'mal_id',
        'is_read'
    ];

    /**
     * Atributi koji trebaju biti pretvoreni.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Dohvaća stavku kojoj pripada manga.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}