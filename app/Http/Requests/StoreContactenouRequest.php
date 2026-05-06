<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactenouRequest extends FormRequest
{
    /**
     * Tout le monde peut soumettre le formulaire de contact.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        return [
            'prenom'       => ['required', 'string', 'max:100'],
            'nom'          => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email:rfc,dns', 'max:255'],
            'telephone'    => ['nullable', 'string', 'max:30'],
            'sujet'        => ['required', 'string', 'in:info,partenariat,don,benevolat,presse,autre'],
            'message'      => ['required', 'string', 'min:10', 'max:5000'],
            'consentement' => ['accepted'],  // doit être coché (true / "on" / "1")
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'prenom.required'        => 'Le prénom est obligatoire.',
            'nom.required'           => 'Le nom est obligatoire.',
            'email.required'         => "L'adresse e-mail est obligatoire.",
            'email.email'            => "L'adresse e-mail n'est pas valide.",
            'sujet.required'         => 'Veuillez choisir un sujet.',
            'sujet.in'               => 'Le sujet sélectionné est invalide.',
            'message.required'       => 'Le message est obligatoire.',
            'message.min'            => 'Le message doit contenir au moins :min caractères.',
            'message.max'            => 'Le message ne peut pas dépasser :max caractères.',
            'consentement.accepted'  => "Vous devez accepter l'utilisation de vos données.",
        ];
    }

    /**
     * Noms de champs pour les messages d'erreur génériques.
     */
    public function attributes(): array
    {
        return [
            'prenom'       => 'prénom',
            'nom'          => 'nom',
            'email'        => 'adresse e-mail',
            'telephone'    => 'téléphone',
            'sujet'        => 'sujet',
            'message'      => 'message',
            'consentement' => 'consentement',
        ];
    }
}
