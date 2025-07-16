<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\StaffMember;
use App\Models\Visit;
use Illuminate\Http\Request;

class AdminDataController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        // visiteurs présents aujourd'hui
        $visits = Visit::with(['visitor', 'staffMember', 'training'])
            ->whereDate('entered_at', $today)
            ->whereNull('exited_at')
            ->get()
            ->map(function ($visit) {
                // Déterminer le motif de la visite et l'info à afficher
                $visitInfo = '';
                $local = '';
                if ($visit->staff_member_id && $visit->staffMember) {
                    $visitInfo = 'Rendez-vous avec ' . $visit->staffMember->first_name . ' ' . $visit->staffMember->last_name;
                    $local = $visit->staffMember->room;
                } elseif ($visit->training_id && $visit->training) {
                    $visitInfo = 'Formation: ' . $visit->training->title;
                    $local = $visit->training->room ?? '-';
                } else {
                    $visitInfo = $visit->purpose ?? 'Visite générale';
                }




                return [
                    'type' => 'visiteur',
                    'name' => $visit->visitor->first_name . ' ' . $visit->visitor->last_name,
                    'email' => $visit->visitor->email,
                    'info' => $visitInfo,
                    'local' => $local,
                    'time' => $visit->entered_at,
                ];
            });

        // formations prévues aujourd'hui
        $trainings = Training::with('staffMember')
            ->whereDate('date', $today)
            ->get()
            ->map(function ($training) {
                return [
                    'type' => 'formation',
                    'name' => $training->title,
                    'info' => $training->staffMember ? ($training->staffMember->first_name . ' ' . $training->staffMember->last_name) : '-',
                    'local' => $training->room ?? '-',
                    'time' => $training->date,
                ];
            });

        // Fusion et trier par heure
        $entries = $visits->concat($trainings)->sortBy('time');

        return view('admin.dashboard', compact('entries'));
    }

    public function manageData()
    {
        $staffMembers = StaffMember::orderBy('last_name')->get();
        $trainings = Training::with('staffMember')->orderBy('date', 'desc')->get();
        $allStaffMembers = StaffMember::orderBy('last_name')->get(); // Pour le dropdown des formations

        return view('admin.manage-data', compact('staffMembers', 'trainings', 'allStaffMembers'));
    }

    // Méthodes pour le personnel
    public function storeStaff(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'function' => 'required|in:formateur,administration',
            'room' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        StaffMember::create($request->all());

        return redirect()->route('admin.manage-data', ['tab' => 'staff'])
            ->with('success', 'Membre du personnel ajouté avec succès');
    }

    public function editStaff(StaffMember $staff)
    {
        $allStaffMembers = StaffMember::orderBy('last_name')->get();
        $trainings = Training::with('staffMember')->orderBy('date', 'desc')->get();

        return view('admin.manage-data', compact('staff', 'allStaffMembers', 'trainings'))
            ->with('editingStaff', true)
            ->with('tab', 'staff');
    }

    public function updateStaff(Request $request, StaffMember $staff)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'function' => 'required|in:formateur,administration',
            'room' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        $staff->update($request->all());

        return redirect()->route('admin.manage-data', ['tab' => 'staff'])
            ->with('success', 'Membre du personnel modifié avec succès');
    }

    public function destroyStaff(StaffMember $staff)
    {
        $staff->delete();

        return redirect()->route('admin.manage-data', ['tab' => 'staff'])
            ->with('success', 'Membre du personnel supprimé avec succès');
    }

    // Méthodes pour les formations
    public function storeTraining(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'room' => 'required|string|max:255',
            'staff_member_id' => 'required|exists:staff_members,id',
        ]);

        Training::create($request->all());

        return redirect()->route('admin.manage-data', ['tab' => 'formations'])
            ->with('success', 'Formation ajoutée avec succès');
    }

    public function editTraining(Training $training)
    {
        $staffMembers = StaffMember::orderBy('last_name')->get();
        $allStaffMembers = StaffMember::orderBy('last_name')->get();
        $trainings = Training::with('staffMember')->orderBy('date', 'desc')->get();

        return view('admin.manage-data', compact('training', 'staffMembers', 'allStaffMembers', 'trainings'))
            ->with('editingTraining', true)
            ->with('tab', 'formations');
    }

    public function updateTraining(Request $request, Training $training)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'room' => 'required|string|max:255',
            'staff_member_id' => 'required|exists:staff_members,id',
        ]);

        $training->update($request->all());

        return redirect()->route('admin.manage-data', ['tab' => 'formations'])
            ->with('success', 'Formation modifiée avec succès');
    }

    public function destroyTraining(Training $training)
    {
        $training->delete();

        return redirect()->route('admin.manage-data', ['tab' => 'formations'])
            ->with('success', 'Formation supprimée avec succès');
    }

    public function history()
    {
        $search = request('search');

        // Récupérer tous les visiteurs avec recherche
        $visitsQuery = Visit::with(['visitor', 'staffMember', 'training'])
            ->orderBy('entered_at', 'desc');

        if ($search) {
            $visitsQuery->where(function ($query) use ($search) {
                $query->whereHas('visitor', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('staffMember', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('room', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('training', function ($q) use ($search) {
                        $q->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('room', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('purpose', 'LIKE', "%{$search}%");
            });
        }

        $visits = $visitsQuery->get()->map(function ($visit) {
            // Déterminer le motif de la visite et l'info à afficher
            $visitInfo = '';
            $local = '';
            if ($visit->staff_member_id && $visit->staffMember) {
                $visitInfo = 'Rendez-vous avec ' . $visit->staffMember->first_name . ' ' . $visit->staffMember->last_name;
                $local = $visit->staffMember->room;
            } elseif ($visit->training_id && $visit->training) {
                $visitInfo = 'Formation: ' . $visit->training->title;
                $local = $visit->training->room ?? '-';
            } else {
                $visitInfo = $visit->purpose ?? 'Visite générale';
                $local = '-';
            }

            return [
                'type' => 'visiteur',
                'name' => $visit->visitor->first_name . ' ' . $visit->visitor->last_name,
                'email' => $visit->visitor->email,
                'info' => $visitInfo,
                'local' => $local,
                'time' => $visit->entered_at,
                'date' => \Carbon\Carbon::parse($visit->date)->format('d/m/Y'), // Format de la date pour l'affichage
                'exit_time' => $visit->exited_at,
                'sort_date' => $visit->entered_at, // Pour le tri
            ];
        });

        // Récupérer toutes les formations avec recherche
        $trainingsQuery = Training::with('staffMember')
            ->orderBy('date', 'desc');

        if ($search) {
            $trainingsQuery->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('room', 'LIKE', "%{$search}%")
                    ->orWhereHas('staffMember', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $trainings = $trainingsQuery->get()->map(function ($training) {
            return [
                'type' => 'formation',
                'name' => $training->title,
                'email' => '-',
                'info' => $training->staffMember ?
                    'Formateur: ' . $training->staffMember->first_name . ' ' . $training->staffMember->last_name :
                    'Formateur non assigné',
                'local' => $training->room ?? '-',
                'time' => null, // Pas d'heure pour les formations
                'date' => \Carbon\Carbon::parse($training->date)->format('d/m/Y'),
                'exit_time' => null, // Pas d'heure de sortie pour les formations
                'sort_date' => \Carbon\Carbon::parse($training->date), // Pour le tri
            ];
        });

        // Fusionner et trier par date décroissante (du plus récent au plus ancien)
        $histories = $visits->concat($trainings)
            ->sortByDesc('sort_date')
            ->values(); // Réindexer la collection

        return view('admin.history', compact('histories'));
    }
}
