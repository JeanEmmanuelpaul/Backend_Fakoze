<?php

namespace App\Http\Controllers;

 use App\Http\Controllers\Controller;
 use App\Models\Evenement;
use Illuminate\Http\Request;

class EvenementController extends Controller
{
    public function index()
    {
        $evenements = Evenement::latest()->get();
        return response()->json([
            'data'   => $evenements,
            'Status' => 200,
        ]);
    }

    public function show($id)
    {
        return response()->json(
            Evenement::findOrFail($id)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'  => 'required|string|max:255',
            'date'   => 'required|date',
            'statut' => 'in:planifié,en_cours,terminé,annulé',
        ]);

        $evenement = Evenement::create([
            'titre'       => $request->titre,
            'lieu'        => $request->lieu,
            'date'        => $request->date,
            'description' => $request->description,
            'image'       => $request->image,
            'statut'      => $request->statut ?? 'planifié',
            'capacite'    => $request->capacite,
        ]);

        return response()->json([
            'Message'    => 'Événement créé avec succès.',
            'evenement'  => $evenement,
            'Status'     => 201,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titre'  => 'required|string|max:255',
            'date'   => 'required|date',
            'statut' => 'in:planifié,en_cours,terminé,annulé',
        ]);

        $evenement = Evenement::findOrFail($id);

        $evenement->update([
            'titre'       => $request->titre,
            'lieu'        => $request->lieu,
            'date'        => $request->date,
            'description' => $request->description,
            'image'       => $request->image,
            'statut'      => $request->statut,
            'capacite'    => $request->capacite,
        ]);

        return response()->json([
            'Message'   => 'Événement mis à jour avec succès.',
            'evenement' => $evenement,
            'Status'    => 200,
        ]);
    }

    public function destroy($id)
    {
        $evenement = Evenement::findOrFail($id);
        $evenement->delete();

        return response()->json([
            'Message' => 'Événement supprimé avec succès.',
            'Status'  => 200,
        ]);
    }
}
