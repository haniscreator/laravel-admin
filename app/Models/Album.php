<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'keyword',
        'location',
        'status',
        'created_date',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function coverImage()
    {
        return $this->hasOne(\App\Models\Image::class, 'parent_id')
            ->where('type', 'album');
    }
}
