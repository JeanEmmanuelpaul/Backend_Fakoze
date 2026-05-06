<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    // GET /api/about/1
    public function show(Request $request, $id)
    {
        $about = About::findOrFail($id);
        return response()->json($about);
    }

    // PUT /api/about/1
   public function update(Request $request, $id)
{
    $about = About::findOrFail($id);

    $request->validate([
        'missons'     => 'nullable|string',
        'vision'      => 'nullable|string',
        'description' => 'nullable|string',
        'qui'         => 'nullable|string',
        'imagem'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'imagev'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'imaged'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'imageq'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $data = $request->only(['missons', 'vision', 'description', 'qui']);

    // ── Upload chaque image si présente ──
    foreach (['imagem', 'imagev', 'imaged', 'imageq'] as $field) {
        if ($request->hasFile($field)) {
            // Supprimer l'ancienne image
            if ($about->$field) {
                Storage::disk('public')->delete($about->$field);
            }
            $data[$field] = $request->file($field)->store('about', 'public');
        }
    }

    $about->update($data);

    return response()->json([
        'status'  => true,
        'message' => 'Mis à jour avec succès',
        'about'   => $about
    ]);
}
    public function store(Request $request)
{
    $validated = $request->validate([
        'missons'     => 'nullable|string',
        'imagem'      => 'nullable|string',
        'vision'      => 'nullable|string',
        'imagev'      => 'nullable|string',
        'description' => 'nullable|string',
        'imaged'      => 'nullable|string',
        'qui'         => 'nullable|string',
        'imageq'      => 'nullable|string',
    ]);

    $about = About::create($validated);

    return response()->json([
        'message' => 'About créé avec succès.',
        'about'   => $about,
    ], 201);
}
}
