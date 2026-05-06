<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Galerie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalerieController extends Controller
{
    // GET — toutes les galeries (avec l'article lié)
   public function index(Request $request)
    {
        $request->validate([
            'article_id' => 'required|integer|exists:galeries,article_id',
        ]);

        $galeries = Galerie::where('article_id', $request->article_id)->get();

        if ($galeries->isEmpty()) {
            return response()->json([], 200);
        }

        return response()->json($galeries, 200);
    }

    // GET — galeries d'un article spécifique
    public function byArticle($article_id)
    {
        $galeries = Galerie::where('article_id', $article_id)
                           ->orderBy('created_at', 'desc')
                           ->get();

        return response()->json([
            'galeries' => $galeries,
            'Status'   => 200
        ]);
    }

    // GET — une seule galerie
    public function show($id)
    {
        $galerie = Galerie::with('article')->findOrFail($id);

        return response()->json([
            'galerie' => $galerie,
            'Status'  => 200
        ]);
    }

    // POST — créer une galerie
    public function store(Request $request)
    {
        $request->validate([
            'article_id'  => 'required|exists:articles,id',
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image1'      => 'nullable|string',
            'image2'      => 'nullable|string',
            'image3'      => 'nullable|string',
            'image4'      => 'nullable|string',
            'image5'      => 'nullable|string',
            'image6'      => 'nullable|string',
            'image7'      => 'nullable|string',
            'image8'      => 'nullable|string',
            'image9'      => 'nullable|string',
            'image10'     => 'nullable|string',
        ]);

        $galerie = Galerie::create([
            'article_id'  => $request->article_id,
            'titre'       => $request->titre,
            'description' => $request->description,
            'image1'      => $request->image1,
            'image2'      => $request->image2,
            'image3'      => $request->image3,
            'image4'      => $request->image4,
            'image5'      => $request->image5,
            'image6'      => $request->image6,
            'image7'      => $request->image7,
            'image8'      => $request->image8,
            'image9'      => $request->image9,
            'image10'     => $request->image10,
        ]);

        return response()->json([
            'Message' => 'Galerie créée avec succès.',
            'galerie' => $galerie->load('article'),
            'Status'  => 201
        ], 201);
    }

    // PUT — modifier une galerie
    public function update(Request $request, $id)
    {
        $request->validate([
            'article_id'  => 'required|exists:articles,id',
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image1'      => 'nullable|string',
            'image2'      => 'nullable|string',
            'image3'      => 'nullable|string',
            'image4'      => 'nullable|string',
            'image5'      => 'nullable|string',
            'image6'      => 'nullable|string',
            'image7'      => 'nullable|string',
            'image8'      => 'nullable|string',
            'image9'      => 'nullable|string',
            'image10'     => 'nullable|string',
        ]);

        $galerie = Galerie::findOrFail($id);

        // Supprimer les anciennes images remplacées
        for ($i = 1; $i <= 10; $i++) {
            $field   = "image{$i}";
            $newPath = $request->$field;
            $oldPath = $galerie->$field;

            if ($newPath && $newPath !== $oldPath && $oldPath) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $galerie->update([
            'article_id'  => $request->article_id,
            'titre'       => $request->titre,
            'description' => $request->description,
            'image1'      => $request->image1,
            'image2'      => $request->image2,
            'image3'      => $request->image3,
            'image4'      => $request->image4,
            'image5'      => $request->image5,
            'image6'      => $request->image6,
            'image7'      => $request->image7,
            'image8'      => $request->image8,
            'image9'      => $request->image9,
            'image10'     => $request->image10,
        ]);

        return response()->json([
            'Message' => 'Galerie mise à jour avec succès.',
            'galerie' => $galerie->load('article'),
            'Status'  => 200
        ]);
    }

    // DELETE — supprimer une galerie + toutes ses images
    public function destroy($id)
    {
        $galerie = Galerie::findOrFail($id);

        // Supprimer toutes les images du disque
        for ($i = 1; $i <= 10; $i++) {
            $field = "image{$i}";
            if ($galerie->$field && Storage::disk('public')->exists($galerie->$field)) {
                Storage::disk('public')->delete($galerie->$field);
            }
        }

        $galerie->delete();

        return response()->json([
            'Message' => 'Galerie supprimée avec succès.',
            'Status'  => 200
        ]);
    }
}
