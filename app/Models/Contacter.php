<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacter extends Model
{
    //
    protected $fillable = [

    'prenom',
    'nom',
    'email',
    'telephone',
    'sujet',
    'message'
    ];
}
