<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    //
      public function index()
    {
        $sliders = Slider::latest()->get()->map(function ($slider) {
            if ($slider->image) {
                $slider->image = asset('storage/' . $slider->image);
            }
            return $slider;
        });

        return response()->json([
            'status'  => true,
            'sliders' => $sliders
        ]);
    }



     // ── Créer ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['titre', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider = Slider::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Slider créé avec succès',
            'slider'  => $slider
        ], 201);
    }


     // ── Modifier ───────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $request->validate([
            'titre'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['titre', 'description']);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Slider mis à jour',
            'slider'  => $slider
        ]);
    }

    // ── Supprimer ──────────────────────────────────────────────────
    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Slider supprimé'
        ]);
    }
}
