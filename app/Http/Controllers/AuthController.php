<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ── Liste tous les utilisateurs ──────────────────────────────────────────
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json([
            'users'  => $users,
            'status' => 200,
        ]);
    }

            public function show($id)
        {
            $user = User::findOrFail($id);
            return response()->json(['user' => $user]);
        }
     public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    // ── Inscription ──────────────────────────────────────────────────────────
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'firstname' => 'required|string|max:100',
            'lastname'  => 'required|string|max:100',
            'email'     => 'required|email|unique:users|max:155',
            'numero'    => 'required|string|max:20',
            'password'  => 'required|min:8',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
                'status' => 422,
            ], 422);
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
            'numero'    => $request->numero,
            'password'  => Hash::make($request->password),
            'role'      => 'membre',
            'status'    => 'actif',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'type'  => 'Bearer',
            'user'  => $user,
        ], 201);
    }

    // ── Connexion ────────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Cherche l'utilisateur
        $user = User::where('email', $request->email)->first();

        // Vérifie existence + mot de passe
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

        // Vérifie le statut du compte
        if ($user->status === 'inactif') {
            return response()->json([
                'status'  => false,
                'message' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ], 403);
        }

        // Révoque les anciens tokens (évite l'accumulation)
        $user->tokens()->delete();

        // Crée un nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Redirection selon rôle
        $redirect = $user->role === 'admin' ? '/Admin/Dashboard' : '/Home';

        return response()->json([
            'status'   => true,
            'message'  => 'Connexion réussie.',
            'token'    => $token,
            'type'     => 'Bearer',
            'role'     => $user->role,
            'redirect' => $redirect,
            'user'     => $user,
        ], 200);
    }

    // ── Déconnexion ──────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Déconnexion réussie.',
        ]);
    }

    // ── Modifier un utilisateur ──────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'firstname' => 'sometimes|string|max:100',
            'lastname'  => 'sometimes|string|max:100',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'role'      => 'sometimes|in:admin,membre,bénévole,donateur',
            'status'    => 'sometimes|in:actif,inactif',
            'numero'    => 'sometimes|string|max:20',
            'adresse'   => 'sometimes|string|max:255',
            'avatar'    => 'sometimes|string',
            'password'  => 'sometimes|min:8',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ], 422);
        }

        $data = $request->only([
            'firstname', 'lastname', 'email',
            'role', 'status', 'numero', 'adresse', 'avatar',
        ]);

        // Hash le mot de passe si fourni
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Utilisateur mis à jour.',
            'user'    => $user,
        ]);
    }

    // ── Supprimer un utilisateur ─────────────────────────────────────────────
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->tokens()->delete(); // supprime les tokens d'abord
        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé.',
        ]);
    }
}
