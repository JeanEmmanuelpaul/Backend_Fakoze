<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // GET — tous les articles
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->get();

        return response()->json([
            'Article' => $articles,
            'Status'  => 200
        ]);
    }

    // GET — un seul article par ID
    public function show($id)   // ✅ $id en int, pas Request
    {
        $article = Article::findOrFail($id);

        return response()->json([
            'Article' => $article,
            'Status'  => 200
        ]);
    }

    // GET — le plus récent
    public function latest()
    {
        $article = Article::orderBy('created_at', 'desc')->first();

        return response()->json([
            'Article' => $article,
            'Status'  => 200
        ]);
    }

    // POST — upload image uniquement (appelé depuis React avant store)
    // Retourne le chemin : "Articles/WpDAw1N8kx4z...jpg"
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp,gif|max:5120',
        ]);

        // Stocke dans storage/app/public/Articles/
        $path = $request->file('image')->store('Articles', 'public');

        return response()->json([
            'path'   => $path,   // ← "Articles/WpDAw1N8kx4z...jpg"
            'Status' => 200
        ]);
    }

    // POST — créer un article (reçoit image en string chemin)
    public function store(Request $request)
    {
        $request->validate([
            'titre'  => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'image'  => 'nullable|string',   // chemin déjà uploadé
        ]);

        $article = Article::create([
            'titre'            => $request->titre,
            'image'            => $request->image,           // "Articles/xxx.jpg"
            'lieu'             => $request->lieu,
            'description1'     => $request->description1,
            'description2'     => $request->description2,
            'description3'     => $request->description3,
            'sou_description1' => $request->sou_description1,
            'sou_description2' => $request->sou_description2,
            'resume'           => $request->resume,
            'resumearticle'    => $request->resumearticle,
            'categorie'        => $request->categorie,
            'auteur'           => $request->auteur,
        ]);

        return response()->json([
            'Message' => 'Article créé avec succès.',
            'Article' => $article,
            'Status'  => 201
        ], 201);
    }

    // PUT — modifier un article
    public function update(Request $request, $id)   // ✅ $id en int, pas Request
    {
        $request->validate([
            'titre'  => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'image'  => 'nullable|string',
        ]);

        $article = Article::findOrFail($id);

        // ── Si une nouvelle image est fournie, supprimer l'ancienne ──────────
        if ($request->filled('image') && $request->image !== $article->image) {
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }
        }

        $article->update([
            'titre'            => $request->titre,
            'image'            => $request->image ?? $article->image,
            'lieu'             => $request->lieu,
            'description1'     => $request->description1,
            'description2'     => $request->description2,
            'description3'     => $request->description3,
            'sou_description1' => $request->sou_description1,
            'sou_description2' => $request->sou_description2,
            'resume'           => $request->resume,
            'resumearticle'    => $request->resumearticle,
            'categorie'        => $request->categorie,
            'auteur'           => $request->auteur,
        ]);

        return response()->json([
            'Message' => 'Article mis à jour avec succès.',
            'Article' => $article,
            'Status'  => 200
        ]);
    }

    // DELETE — supprimer un article + son image
    public function destroy($id)   // ✅ $id en int, pas Request
    {
        $article = Article::findOrFail($id);

        // Supprimer l'image du stockage si elle existe
        if ($article->image && Storage::disk('public')->exists($article->image)) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json([
            'Message' => 'Article supprimé avec succès.',
            'Status'  => 200
        ]);
    }
}
