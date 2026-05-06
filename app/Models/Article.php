<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
     protected $fillable = [
        'titre',
        'image',
        'lieu',
        'description1',
        'description2',
        'description3',
        'sou_description1',
        'sou_description2',
        'resume',
        'resumearticle',
        'categorie',
        'auteur',
    ];

}
