<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
      public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'           => 'required|string|max:255',
            'image'           => 'nullable|string|max:500',
            'lieu'            => 'nullable|string|max:255',
            'description1'    => 'nullable|string',
            'description2'    => 'nullable|string',
            'description3'    => 'nullable|string',
            'sou_description1'=> 'nullable|string',
            'sou_description2'=> 'nullable|string',
            'resume'          => 'nullable|string',
            'resumearticle'   => 'nullable|string',
            'categorie'       => 'nullable|string|max:100',
            'auteur'          => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.max'      => 'Le titre ne peut pas dépasser 255 caractères.',
        ];
    }
}
