<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'album_id',
        'name',
        'description',
        'keyword',
        'media_url',
        'status',
        'created_by',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
