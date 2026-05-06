<?php

namespace App\Http\Controllers;

use App\Models\Visite;
use Illuminate\Http\Request;


class VisiteController extends Controller
{
    // POST — enregistrer une visite
    public function track(Request $request)
    {
        // Éviter de compter plusieurs fois la même IP sur la même page le même jour
        $existe = Visite::where('ip',   $request->ip())
                        ->where('page', $request->page ?? '/')
                        ->whereDate('created_at', today())
                        ->exists();

         if (!$existe) {
            Visite::create([
                'ip'         => $request->ip(),
                'page'       => $request->page ?? '/',
                'user_agent' => $request->userAgent(),
            ]);
        }

        return response()->json([
            'Message' => 'Visite enregistrée.',
            'Status'  => 200,
        ]);
    }

    // GET — total visites (pour dashboard)
    public function index()
    {
        $total = Visite::count();

        $aujourdhui = Visite::whereDate('created_at', today())->count();

        $semaine = Visite::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();

        $parMois = Visite::selectRaw('MONTH(created_at) as mois_num, COUNT(*) as visites')
            ->whereYear('created_at', now()->year)
            ->groupBy('mois_num')
            ->pluck('visites', 'mois_num');

        $mois = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

        $data = collect(range(1, 12))->map(fn($m) => [
            'mois'    => $mois[$m - 1],
            'visites' => $parMois[$m] ?? 0,
        ]);

        return response()->json([
            'total'      => $total,
            'aujourdhui' => $aujourdhui,
            'semaine'    => $semaine,
            'par_mois'   => $data,
            'Status'     => 200,
        ]);
    }
}
