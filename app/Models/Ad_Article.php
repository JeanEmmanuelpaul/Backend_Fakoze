<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad_Article extends Model
{
    //
     protected $fillable = [
        'title',
        'description',
        'date',
        'redacteur',
        'media',
        'email',
        'types'
    ];
}
