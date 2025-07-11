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
    public function enter(Request $request){
        
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'purpose' => 'required|in:visite, formation',
            'staff_member_id' => 'nullable|exists:staff_members,id',
            'training_id' => 'nullable|exists:trainings, id',
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
        ]);
        
    }

}
