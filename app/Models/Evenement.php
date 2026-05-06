<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    //
     protected $table    = 'evenements';
    protected $fillable = ['titre', 'lieu','statut', 'image','date', 'description' ,'capacite'];
    protected $casts    = ['date' => 'datetime'];
}
