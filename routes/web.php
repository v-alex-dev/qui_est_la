
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\AdminDataController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Page de gestion des données (onglets)
    Route::get('/dashboard', [AdminDataController::class, 'dashboard'])->name('dashboard');
    Route::get('/manage-data', [AdminDataController::class, 'manageData'])->name('manage-data');

    // Formations
    Route::post('/trainings', [AdminDataController::class, 'storeTraining'])->name('trainings.store');
    Route::get('/trainings/{training}/edit', [AdminDataController::class, 'editTraining'])->name('trainings.edit');
    Route::put('/trainings/{training}', [AdminDataController::class, 'updateTraining'])->name('trainings.update');
    Route::delete('/trainings/{training}', [AdminDataController::class, 'destroyTraining'])->name('trainings.destroy');

    // Personnel
    Route::post('/staff', [AdminDataController::class, 'storeStaff'])->name('staff.store');
    Route::get('/staff/{staff}/edit', [AdminDataController::class, 'editStaff'])->name('staff.edit');
    Route::put('/staff/{staff}', [AdminDataController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{staff}', [AdminDataController::class, 'destroyStaff'])->name('staff.destroy');
});
require __DIR__ . '/auth.php';
