<?php

namespace App\Http\Controllers;

use App\Models\Impact;
use Illuminate\Http\Request;

class ImpactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $TotalImpact=Impact::all();
        return response()->json(
            [
                'Impact'=>$TotalImpact,
                'Status'=>200
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(Request $request, $id)
{
    $impact = Impact::findOrFail($id);

    $validated = $request->validate([
        'membres'     => 'nullable|integer|min:0',
        'projets'     => 'nullable|integer|min:0',
        'experiences' => 'nullable|integer|min:0',
        'partenaires' => 'nullable|integer|min:0',
    ]);

    $impact->update($validated);

    return response()->json([
        'message' => 'Impact mis à jour.',
        'impact'  => $impact,
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(Impact $impact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Impact $impact)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Impact $impact)
    {
        //
    }
}
