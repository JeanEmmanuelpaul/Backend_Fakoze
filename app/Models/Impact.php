<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impact extends Model
{
    //
    protected $fillable = [

        'membres'     ,
        'projets'     ,
        'experiences' ,
        'partenaires'
    ];
}
