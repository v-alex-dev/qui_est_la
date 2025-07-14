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
}
