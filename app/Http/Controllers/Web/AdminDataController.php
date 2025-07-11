<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\Visit;
use Illuminate\Http\Request;

class AdminDataController extends Controller
{
    //
    public function index()
    {
        return view('admin.data');
    }

    public function dashboard()
    {
        $today = now()->toDateString();

        // visiteurs présents aujourd'hui
        $visits = Visit::with('visitor')
        ->whereDate('entered_at', $today)
        ->whereNull('exited_at')
        ->get()
        ->map(function ($visit) {
            return [
                'type' => 'visiteur',
                'name' => $visit->visitor->first_name . ' ' . $visit->visitor->last_name,
                'info' => $visit->visitor->email,
                'local' => $visit->local ?? '-',
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
                'local' => $training->local ?? '-',
                'time' => $training->date,
            ];
        });

        // Fusion et trier par heure
        $entries = $visits->concat($trainings)->sortBy('time');

        return view('admin.dashboard', compact('entries'));
    }
}
