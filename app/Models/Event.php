<?php

namespace App\Models;

use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  protected $fillable = [
    'titre',
    'lieu',
    'type'
  ];
}
