<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    //   //
     public function index()
    {
        $evenements = Evenement::orderBy('date', 'asc')->get();

        return response()->json([
            'evenements' => $evenements,
            'Status'     => 200
        ]);
    }

    // GET — un seul événement par ID
    public function show($id)
    {
        $evenement = Evenement::findOrFail($id);

        return response()->json([
            'event'  => $evenement,
            'Status' => 200
        ]);
    }

    // GET — le plus récent
    public function latest()
    {
        $evenement = Evenement::orderBy('created_at', 'desc')->first();

        return response()->json([
            'event'  => $evenement,
            'Status' => 200
        ]);
    }

    // POST — upload image uniquement
    // Retourne le chemin : "Evenements/WpDAw1N8kx4z...jpg"
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp,gif|max:5120',
        ]);

        $path = $request->file('image')->store('Evenements', 'public');

        return response()->json([
            'path'   => $path,   // ← "Evenements/WpDAw1N8kx4z...jpg"
            'Status' => 200
        ]);
    }

    // POST — créer un événement (reçoit image en string chemin)
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'date'  => 'required|date',
            'image' => 'nullable|string',
        ]);

        $evenement = Evenement::create([
            'titre'       => $request->titre,
            'lieu'        => $request->lieu,
            'date'        => $request->date,
            'description' => $request->description,
            'image'       => $request->image,        // "Evenements/xxx.jpg"
            'statut'      => $request->statut ?? 'planifié',
            'capacite'    => $request->capacite,
        ]);

        return response()->json([
            'Message'    => 'Événement créé avec succès.',
            'evenement'  => $evenement,
            'Status'     => 201
        ], 201);
    }

    // PUT — modifier un événement
    public function update(Request $request, $id)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'date'  => 'required|date',
            'image' => 'nullable|string',
        ]);

        $evenement = Evenement::findOrFail($id);

        // Si nouvelle image, supprimer l'ancienne du disque
        if ($request->filled('image') && $request->image !== $evenement->image) {
            if ($evenement->image && Storage::disk('public')->exists($evenement->image)) {
                Storage::disk('public')->delete($evenement->image);
            }
        }

        $evenement->update([
            'titre'       => $request->titre,
            'lieu'        => $request->lieu,
            'date'        => $request->date,
            'description' => $request->description,
            'image'       => $request->image ?? $evenement->image,
            'statut'      => $request->statut ?? $evenement->statut,
            'capacite'    => $request->capacite,
        ]);

        return response()->json([
            'Message'   => 'Événement mis à jour avec succès.',
            'evenement' => $evenement,
            'Status'    => 200
        ]);
    }

    // DELETE — supprimer un événement + son image
    public function destroy($id)
    {
        $evenement = Evenement::findOrFail($id);

        // Supprimer l'image du stockage si elle existe
        if ($evenement->image && Storage::disk('public')->exists($evenement->image)) {
            Storage::disk('public')->delete($evenement->image);
        }

        $evenement->delete();

        return response()->json([
            'Message' => 'Événement supprimé avec succès.',
            'Status'  => 200
        ]);
    }
}
