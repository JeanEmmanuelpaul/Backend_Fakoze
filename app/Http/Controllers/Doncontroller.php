<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Don;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

class DonController extends Controller
{
    // ── Taux de conversion (1 USD = 110 HTG) ─────────────────────────────────
    private const HTG_TO_USD = 110;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/dons/create-intent
    // Crée un PaymentIntent Stripe et enregistre le don en BDD (statut: pending)
    // ─────────────────────────────────────────────────────────────────────────
    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'montant'   => 'required|integer|min:100',          // en HTG
            'frequence' => 'required|in:unique,mensuel',
            'message'   => 'nullable|string|max:500',
        ]);

        $montantHTG = (int) $validated['montant'];
        $montantUSD = round($montantHTG / self::HTG_TO_USD, 2); // pour info/logs

        try {
            // ── Stripe charge en centimes USD (minimum $0.50) ─────────────
            // Convertir HTG → USD → centimes USD
            $montantCentimes = (int) round($montantUSD * 100);

            if ($montantCentimes < 50) {
                return response()->json([
                    'message' => 'Montant trop faible (minimum ~55 HTG / $0.50 USD).',
                ], 422);
            }

            // ── Créer le PaymentIntent ─────────────────────────────────────
            $intent = PaymentIntent::create([
                'amount'   => $montantCentimes,
                'currency' => 'usd',                            // Stripe facture en USD
                'metadata' => [
                    'montant_htg'  => $montantHTG,
                    'montant_usd'  => $montantUSD,
                    'frequence'    => $validated['frequence'],
                    'message'      => $validated['message'] ?? '',
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            // ── Enregistrer en BDD (statut pending) ───────────────────────
            $don = Don::create([
                'montant_htg'       => $montantHTG,
                'montant_usd'       => $montantUSD,
                'frequence'         => $validated['frequence'],
                'message'           => $validated['message'] ?? null,
                'stripe_intent_id'  => $intent->id,
                'statut'            => 'pending',
                'donateur_id'       => auth(), // null si non connecté
            ]);

            return response()->json([
                'client_secret' => $intent->client_secret,
                'don_id'        => $don->id,
                'montant_htg'   => $montantHTG,
                'montant_usd'   => $montantUSD,
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'message' => 'Erreur Stripe : ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/dons/{id}/confirm
    // Confirme le don en BDD après succès Stripe côté client
    // ─────────────────────────────────────────────────────────────────────────
    public function confirm(Request $request, int $id): JsonResponse
    {
        $don = Don::findOrFail($id);

        // Vérifier l'état réel sur Stripe (évite les confirmations frauduleuses)
        try {
            $intent = PaymentIntent::retrieve($don->stripe_intent_id);

            if ($intent->status !== 'succeeded') {
                return response()->json([
                    'message' => 'Paiement non confirmé par Stripe (statut : ' . $intent->status . ').',
                ], 422);
            }

        } catch (ApiErrorException $e) {
            return response()->json([
                'message' => 'Impossible de vérifier le paiement : ' . $e->getMessage(),
            ], 500);
        }

        $don->update(['statut' => 'completed']);

        // TODO: envoyer reçu par email
        // Mail::to($don->donateur->email)->send(new RecuDonMail($don));

        return response()->json([
            'message'     => 'Don confirmé avec succès.',
            'don_id'      => $don->id,
            'montant_htg' => $don->montant_htg,
            'montant_usd' => $don->montant_usd,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/dons/webhook  (à appeler depuis Stripe Dashboard)
    // Webhook Stripe pour sécurité renforcée (paiements confirmés côté serveur)
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Payload invalide'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Signature invalide'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                Don::where('stripe_intent_id', $intent->id)
                   ->where('statut', 'pending')
                   ->update(['statut' => 'completed']);
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                Don::where('stripe_intent_id', $intent->id)
                   ->update(['statut' => 'failed']);
                break;
        }

        return response()->json(['received' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/dons  (admin ou usage interne)
    // Liste les dons avec pagination
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $dons = Don::latest()
            ->paginate(20);

        return response()->json($dons);
    }
}
