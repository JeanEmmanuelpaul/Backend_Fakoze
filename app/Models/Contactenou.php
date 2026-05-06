<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contactenou extends Model
{
    use HasFactory;

    // ── Nom exact de la table en base ─────────────────────────────────────────
    protected $table = 'contactenou';

    /**
     * Champs autorisés en masse assignment.
     */
    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'telephone',
        'sujet',
        'message',
        'consentement',
        'statut',
        'ip_address',
    ];

    /**
     * Casts automatiques.
     */
    protected $casts = [
        'consentement' => 'boolean',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Libellés lisibles pour les sujets.
     */
    public static array $sujets = [
        'info'        => "Demande d'information",
        'partenariat' => 'Partenariat',
        'don'         => 'Don / soutien financier',
        'benevolat'   => 'Bénévolat',
        'presse'      => 'Presse / média',
        'autre'       => 'Autre',
    ];

    /**
     * Accesseur : libellé lisible du sujet.
     */
    public function getSujetLabelAttribute(): string
    {
        return self::$sujets[$this->sujet] ?? $this->sujet;
    }

    /**
     * Accesseur : nom complet du contact.
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeNouveaux($query)
    {
        return $query->where('statut', 'nouveau');
    }

    public function scopeParStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }
}
