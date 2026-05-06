<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galerie extends Model
{
    use HasFactory;

    protected $table = 'galeries';

    protected $fillable = [
        'article_id',
        'titre',
        'description',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'image6',
        'image7',
        'image8',
        'image9',
        'image10',
    ];

    // ── Relation : une galerie appartient à un article ────────────────────────
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
