<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Don extends Model
{
    use HasFactory;

    protected $table = 'dons';

    protected $fillable = [
        'montant',
        'frequence',
        'message',
        'stripe_payment_intent_id',
        'stripe_client_secret',
        'statut',          // pending | succeeded | failed
        'email_recu',      // true quand le reçu est envoyé
    ];

    protected $casts = [
        'montant'     => 'integer',
        'email_recu'  => 'boolean',
    ];

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeSucceeded($query)
    {
        return $query->where('statut', 'succeeded');
    }

    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }
}
