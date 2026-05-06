<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visite extends Model
{
    //
     protected $table    = 'visites';
    protected $fillable = ['ip', 'page', 'user_agent'];
}
