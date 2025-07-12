  <?php

  use App\Http\Controllers\Api\VisitorController;
  use Illuminate\Support\Facades\Route;

  Route::prefix('v1')->group(function () {
    Route::post('/enter', [VisitorController::class, 'enter']);
    Route::post('/exit', [VisitorController::class, 'exit']);
    Route::post('/return', [VisitorController::class, 'returnVisitor']);
    Route::get('/visitor/search', [VisitorController::class, 'searchByEmail']);
    Route::get('/staff-members', [VisitorController::class, 'getStaffMembers']);
    Route::get('/trainings/today', [VisitorController::class, 'getTodayTrainings']);
  });
