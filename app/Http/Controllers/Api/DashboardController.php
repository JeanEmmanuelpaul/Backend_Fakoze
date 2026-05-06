<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Models\Don;
use App\Models\Evenement;
use App\Models\Visite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // ── Stats globales ──────────────────────────────────
    public function stats()
    {
        $now       = now();
        $lastMonth = now()->subMonth();

        // Totaux
        $totalUsers     = User::count();
        $totalArticles  = Article::count();
        $totalDonateurs = User::where('role', 'Donateur')->count();
        $totalVisites   = Visite::count();

        // Tendances (comparaison mois précédent)
        $usersThisMonth  = User::whereMonth('created_at', $now->month)->count();
        $usersLastMonth  = User::whereMonth('created_at', $lastMonth->month)->count();
        $trendUsers      = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100).'%'
            : '+0%';

        $articlesThisMonth = Article::whereMonth('created_at', $now->month)->count();
        $articlesLastMonth = Article::whereMonth('created_at', $lastMonth->month)->count();
        $trendArticles     = $articlesLastMonth > 0
            ? round((($articlesThisMonth - $articlesLastMonth) / $articlesLastMonth) * 100).'%'
            : '+0%';

        $donateursMois     = User::where('role', 'Donateur')
                                 ->whereMonth('created_at', $now->month)->count();
        $donateursDernMois = User::where('role', 'Donateur')
                                 ->whereMonth('created_at', $lastMonth->month)->count();
        $trendDonateurs    = $donateursDernMois > 0
            ? round((($donateursMois - $donateursDernMois) / $donateursDernMois) * 100).'%'
            : '+0%';

        $visitesThisMonth = Visite::whereMonth('created_at', $now->month)->count();
        $visitesLastMonth = Visite::whereMonth('created_at', $lastMonth->month)->count();
        $trendVisites     = $visitesLastMonth > 0
            ? round((($visitesThisMonth - $visitesLastMonth) / $visitesLastMonth) * 100).'%'
            : '+0%';

        return response()->json([
            'total_users'      => $totalUsers,
            'total_articles'   => $totalArticles,
            'total_donateurs'  => $totalDonateurs,
            'total_visites'    => $totalVisites,
            'trend_users'      => '+'.$trendUsers,
            'trend_articles'   => '+'.$trendArticles,
            'trend_donateurs'  => '+'.$trendDonateurs,
            'trend_visites'    => '+'.$trendVisites,
            'Status'           => 200,
        ]);
    }

    // ── Derniers membres ────────────────────────────────
    public function users()
    {
        $users = User::latest()
            ->take(10)
            ->get(['id', 'name', 'email', 'role', 'status', 'email_verified_at', 'created_at']);

        return response()->json([
            'users'  => $users,
            'Status' => 200,
        ]);
    }

    // ── Visites & membres par mois ──────────────────────
    public function visits()
    {
        $mois = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

        $visites = Visite::selectRaw('MONTH(created_at) as mois_num, COUNT(*) as visites')
            ->whereYear('created_at', now()->year)
            ->groupBy('mois_num')
            ->pluck('visites', 'mois_num');

        $membres = User::selectRaw('MONTH(created_at) as mois_num, COUNT(*) as membres')
            ->whereYear('created_at', now()->year)
            ->groupBy('mois_num')
            ->pluck('membres', 'mois_num');

        $data = collect(range(1, 12))->map(fn($m) => [
            'mois'    => $mois[$m - 1],
            'visites' => $visites[$m] ?? 0,
            'membres' => $membres[$m] ?? 0,
        ]);

        return response()->json([
            'data'   => $data,
            'Status' => 200,
        ]);
    }

    // ── Dons par mois ───────────────────────────────────
    public function dons()
    {
        $mois = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

        $dons =Don::selectRaw('MONTH(created_at) as mois_num, SUM(montant) as dons')
            ->whereYear('created_at', now()->year)
            ->groupBy('mois_num')
            ->pluck('dons', 'mois_num');

        $data = collect(range(1, 12))->map(fn($m) => [
            'mois' => $mois[$m - 1],
            'dons' => round($dons[$m] ?? 0),
        ]);

        return response()->json([
            'data'   => $data,
            'Status' => 200,
        ]);
    }

    // ── Articles par catégorie ──────────────────────────
    public function categories()
    {
        $data = Article::selectRaw('categorie as name, COUNT(*) as value')
            ->whereNotNull('categorie')
            ->groupBy('categorie')
            ->orderByDesc('value')
            ->get();

        return response()->json([
            'data'   => $data,
            'Status' => 200,
        ]);
    }

    // ── Prochains événements ────────────────────────────
    public function events()
    {
        $data = Evenement::where('date', '>=', now())
            ->orderBy('date')
            ->take(5)
            ->get(['id', 'titre', 'lieu', 'date']);

        return response()->json([
            'data'   => $data,
            'Status' => 200,
        ]);
    }
}
