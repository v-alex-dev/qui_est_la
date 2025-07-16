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
            ]
        );

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'staff_member_id' => $validated['staff_member_id'] ?? null,
            'training_id' => $validated['training_id'] ?? null,
            'purpose' => $validated['purpose'],
            'entered_at' => now(),
            'badge_id' => Str::uuid(),
        ]);

        return response()->json([
            'message' => 'Entrée enregistrée',
            'visit' => $visit,
            'visitor' => $visitor,
            'badge_id' => $visit->badge_id,
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

        $visit = Visit::where('badge_id', $validated['badge_id'])
            ->whereNull('exited_at')
            ->first();

        if (!$visit) {
            return response()->json([
                'error' => 'Aucune visite active trouvée avec cet ID',
            ], 404);
        }

        $visit->update(['exited_at' => now()]);

        return response()->json([
            'message' => 'Sortie enregistrée avec succès',
            'visitor' => $visit->visitor,
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

        // Vérifier si le visiteur existe et a déjà une visite
        $existingVisit = Visit::where('badge_id', $validated['badge_id'])->first();

        if (!$existingVisit) {
            return response()->json([
                'error' => 'Aucune visite trouvée avec cet ID',
            ], 404);
        }

        // Créer une nouvelle visite pour le retour
        $newVisit = Visit::create([
            'visitor_id' => $existingVisit->visitor_id,
            'staff_member_id' => $validated['staff_member_id'] ?? null,
            'training_id' => $validated['training_id'] ?? null,
            'purpose' => $validated['purpose'],
            'entered_at' => now(),
            'badge_id' => $existingVisit->badge_id, // Même badge_id
        ]);

        return response()->json([
            'message' => 'Retour enregistré avec succès',
            'visit' => $newVisit,
            'visitor' => $existingVisit->visitor,
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

        $visit = Visit::with('visitor')->where('badge_id', $validated['badge_id'])->first();

        if (!$visit) {
            return response()->json([
                'error' => 'Aucun visiteur trouvé avec ce QR code',
            ], 404);
        }

        return response()->json([
            'visit' => $visit,
        ]);
    }
}
