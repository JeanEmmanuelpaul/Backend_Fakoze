<?php

namespace App\Http\Controllers;

use App\Models\Contactenou;
use App\Http\Requests\StoreContactenouRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactenouController extends Controller
{
    // ─── API publique (React → Laravel) ──────────────────────────────────────

    /**
     * Enregistre un message de contact dans la table `contactenou`.
     * POST /api/contactenou
     */
    public function store(StoreContactenouRequest $request): JsonResponse
    {
        // validated() retourne uniquement les champs validés par StoreContactenouRequest
        $contact = Contactenou::create([
            ...$request->validated(),
            'consentement' => true,        // la règle 'accepted' garantit que c'est coché
            'ip_address'   => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Votre message a bien été envoyé. Nous vous répondrons sous 48 h.',
            'data'    => [
                'id'          => $contact->id,
                'nom_complet' => $contact->nom_complet,
            ],
        ], 201);
    }


    public function index(Request $request): JsonResponse
    {
        $contacts = Contactenou::query()
            ->when($request->statut, fn($q, $s) => $q->parStatut($s))
            ->latest()
            ->paginate(20);

        return response()->json($contacts);
    }


    public function show(Contactenou $contactenou): JsonResponse
    {
        if ($contactenou->statut === 'nouveau') {
            $contactenou->update(['statut' => 'lu']);
        }

        return response()->json($contactenou);
    }

    public function update(Request $request, Contactenou $contactenou): JsonResponse
    {
        $request->validate([
            'statut' => ['required', 'in:nouveau,lu,traite,archive'],
        ]);

        $contactenou->update(['statut' => $request->statut]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour.',
            'data'    => $contactenou,
        ]);
    }

    /**
     * Supprime un message.
     * DELETE /admin/contactenou/{contactenou}
     */
    public function destroy(Contactenou $contactenou): JsonResponse
    {
        $contactenou->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message supprimé.',
        ]);
    }



        public function sendMail(Request $request, Contactenou $contactenou): JsonResponse
        {
        $request->validate([
            'sujet' => ['required', 'string', 'max:255'],
            'corps' => ['required', 'string', 'min:5'],
        ]);

        Mail::raw($request->corps, function ($message) use ($request, $contactenou) {
            $message
                ->to($contactenou->email, "{$contactenou->prenom} {$contactenou->nom}")
                ->subject($request->sujet)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });

        // Marquer comme traité après envoi du mail
        $contactenou->update(['statut' => 'traite']);

        return response()->json([
            'success' => true,
            'message' => "Mail envoyé à {$contactenou->email}.",
        ]);
        }

}
