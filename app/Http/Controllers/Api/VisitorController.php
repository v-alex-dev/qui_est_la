<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitorController extends Controller
{
    /**
     * Gère l'entrée d'un visiteur (création ou récupération, puis enregistrement de la visite)
     * @param
     */
    public function enter(Request $request)
    {

        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'purpose' => 'required|in:visite,formation',
            'staff_member_id' => 'nullable|exists:staff_members,id',
            'training_id' => 'nullable|exists:trainings,id',
        ]);

        $visitor = Visitor::firstOrCreate(
            ['email' => $validated['email']],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'badge_id' => Str::uuid(),
            ]
        );

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'staff_member_id' => $validated['staff_member_id'] ?? null,
            'training_id' => $validated['training_id'] ?? null,
            'purpose' => $validated['purpose'],
            'entered_at' => now(),
        ]);

        return response()->json([
            'message' => 'Entrée enregistrée',
            'visit' => $visit,
            'visitor' => $visitor,
            'badge_id' => $visitor->badge_id,
        ]);
    }

    /**
     * Gère la sortie d'un visiteur
     */
    public function exit(Request $request)
    {
        $validated = $request->validate([
            'badge_id' => 'required|string',
        ]);

        // Trouver le visiteur par badge_id
        $visitor = Visitor::where('badge_id', $validated['badge_id'])->first();

        if (!$visitor) {
            return response()->json([
                'error' => 'Aucun visiteur trouvé avec cet ID de badge',
            ], 404);
        }

        // Trouver la visite active (sans heure de sortie)
        $visit = Visit::where('visitor_id', $visitor->id)
            ->whereNull('exited_at')
            ->first();

        if (!$visit) {
            return response()->json([
                'error' => 'Aucune visite active trouvée pour ce visiteur',
            ], 404);
        }

        $visit->update(['exited_at' => now()]);

        return response()->json([
            'message' => 'Sortie enregistrée avec succès',
            'visitor' => $visitor,
            'exited_at' => $visit->exited_at,
        ]);
    }

    /**
     * Gère le retour d'un visiteur
     */
    public function returnVisitor(Request $request)
    {
        $validated = $request->validate([
            'badge_id' => 'required|string',
            'purpose' => 'required|in:visite,formation',
            'staff_member_id' => 'nullable|exists:staff_members,id',
            'training_id' => 'nullable|exists:trainings,id',
        ]);

        // Trouver le visiteur par badge_id
        $visitor = Visitor::where('badge_id', $validated['badge_id'])->first();

        if (!$visitor) {
            return response()->json([
                'error' => 'Aucun visiteur trouvé avec cet ID de badge',
            ], 404);
        }

        // Créer une nouvelle visite pour le retour
        $newVisit = Visit::create([
            'visitor_id' => $visitor->id,
            'staff_member_id' => $validated['staff_member_id'] ?? null,
            'training_id' => $validated['training_id'] ?? null,
            'purpose' => $validated['purpose'],
            'entered_at' => now(),
        ]);

        return response()->json([
            'message' => 'Retour enregistré avec succès',
            'visit' => $newVisit,
            'visitor' => $visitor,
        ]);
    }

    /**
     * Recherche un visiteur par email
     */
    public function searchByEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $visitor = Visitor::where('email', $validated['email'])->first();

        if (!$visitor) {
            return response()->json([
                'error' => 'Aucun visiteur trouvé avec cet email',
            ], 404);
        }

        // Récupérer la dernière visite
        $lastVisit = Visit::where('visitor_id', $visitor->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'visitor' => $visitor,
            'last_visit' => $lastVisit,
        ]);
    }

    /**
     * Récupère la liste des membres du personnel
     */
    public function getStaffMembers()
    {
        $staffMembers = \App\Models\StaffMember::orderBy('last_name')->get();

        return response()->json([
            'staff_members' => $staffMembers,
        ]);
    }

    /**
     * Récupère les formations d'aujourd'hui
     */
    public function getTodayTrainings()
    {
        $today = now()->toDateString();
        $trainings = \App\Models\Training::with('staffMember')
            ->whereDate('date', $today)
            ->orderBy('date')
            ->get();

        return response()->json([
            'trainings' => $trainings,
        ]);
    }

    /**
     * Return list visitors by qr code_id
     */
    public function getVisitorByBadgeId(Request $request)
    {
        $validated = $request->validate([
            'badge_id' => 'required|string',
        ]);

        $visitor = Visitor::where('badge_id', $validated['badge_id'])->first();

        if (!$visitor) {
            return response()->json([
                'error' => 'Aucun visiteur trouvé avec ce badge ID',
            ], 404);
        }

        // Récupérer la dernière visite
        $lastVisit = Visit::where('visitor_id', $visitor->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'visitor' => $visitor,
            'last_visit' => $lastVisit,
        ]);
    }
}
