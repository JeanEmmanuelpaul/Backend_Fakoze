<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\GalerieController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactenouController;
use App\Http\Controllers\DonController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ImpactController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\VisiteController;
use Illuminate\Http\Request;
use App\Models\Don;
use Illuminate\Support\Facades\Route;

// Route login admin
Route::post('/admin/login', [AdminAuthController::class, 'authenticate']);



//  tout les route pour les articles
Route::get('Article',              [ArticleController::class, 'index']);
Route::get('Article/{id}',         [ArticleController::class, 'show']);
Route::get('/latest-article',       [ArticleController::class, 'latest']);
// Admin routes Article
Route::post('/Article',             [ArticleController::class, 'store']);
Route::put('/Article/{id}',         [ArticleController::class, 'update']);
Route::delete('/Articles/{id}',      [ArticleController::class, 'destroy']);

// Event Rout
Route::get('Event',              [EventController::class, 'index']);
Route::get('Event/{id}',         [EventController::class, 'show']);
Route::get('Latest-Event',    [EventController::class, 'latest']);
// Admin routes Event
Route::post('Event',             [EventController::class, 'store']);
Route::put('Event/{id}',         [EventController::class, 'update']);
Route::delete('Event/{id}',      [EventController::class, 'destroy']);
Route::post('upload-image-event', [EventController::class, 'uploadImage']);
// Contact Rout
Route::get('/Contact',[ContactController::class,'index']);

Route::post('dons/create-intent', [DonController::class, 'createIntent']);
Route::post('dons/{don_id}/confirm', [DonController::class, 'confirm']);


// Slide Route
Route::get('/sliders', [SliderController::class, 'index']);
Route::post('/sliders', [SliderController::class, 'store']);

// Impact
Route::get('/Impact',[ImpactController::class,'index']);


Route::get('/contactenou',                  [ContactenouController::class, 'index']);
Route::post('/contactenous',                 [ContactenouController::class, 'store']);
Route::get('/contactenou/{contactenou}',    [ContactenouController::class, 'show']);
Route::patch('/contactenou/{contactenou}',  [ContactenouController::class, 'update']);
Route::delete('/contactenou/{contactenou}', [ContactenouController::class, 'destroy']);
Route::post('/contactenou/{contactenou}/mail', [ContactenouController::class, 'sendMail']);

Route::post('/register',[AuthController::class,'register']);
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::post('/login',[AuthController::class,'login']);
Route::get('users',  [AuthController::class,'index']);

Route::get('/users',                [DashboardController::class, 'users']);
Route::get('/dashboard/stats',      [DashboardController::class, 'stats']);
Route::get('dashboard/users',      [DashboardController::class, 'users']);
Route::get('/dashboard/visits',     [DashboardController::class, 'visits']);
Route::get('dons',       [DashboardController::class, 'dons']);
Route::get('/dashboard/categories', [DashboardController::class, 'categories']);
Route::get('/dashboard/events',     [DashboardController::class, 'events']);
Route::post('/about',       [AboutController::class, 'store']);   // ← nouveau
Route::put('/about/{id}',   [AboutController::class, 'update']);
Route::get('/about/{id}',           [AboutController::class, 'show']);
Route::put('/about/{id}',           [AboutController::class, 'update']);
Route::put('/Impact/{id}',     [ImpactController::class, 'update']);
Route::get('/evenements',       [EvenementController::class, 'index']);
Route::get('/evenements/{id}',  [EvenementController::class, 'show']);
Route::post('/evenements',      [EvenementController::class, 'store']);
Route::put('/evenements/{id}',  [EvenementController::class, 'update']);
Route::delete('/evenements/{id}',[EvenementController::class, 'destroy']);
Route::post('/visites/track', [VisiteController::class, 'track']);
Route::post('upload-image', [ArticleController::class, 'uploadImage']);
Route::apiResource('Article', ArticleController::class);
// Galeries

Route::post('galeries', [GalerieController::class,'store']);
Route::get('galerie/{article_id}', [GalerieController::class,'index']);
Route::get('/galeries/{article_id}', [GalerieController::class, 'show']);



// // GET    /api/galeries                    → toutes les galeries
// GET    /api/galeries/{id}               → une galerie
// GET    /api/galeries/article/{article_id} → galeries d'un article
// POST   /api/galeries                    → créer
// PUT    /api/galeries/{id}               → modifier
// DELETE /api/galeries/{id}               → supprimer (+ images supprimées du disque)
//

Route::delete('/sliders/{id}',  [SliderController::class, 'destroy']);
Route::get('/sliders/{id}',  [SliderController::class, 'update']);
// Route::middleware(['auth:sanctum', 'admin'])->group(function () {
  Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::get('/users',        [AuthController::class, 'index']);   // ✅ déjà présent
    Route::delete('/users/{id}',[AuthController::class, 'destroy']); // ← ajouter
    Route::put('/users/{id}',   [AuthController::class, 'update']);  // ← ajouter
// });

// Route protégée (Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
