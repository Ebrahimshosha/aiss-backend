<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TagController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\BookletController;
use App\Http\Controllers\CertificateTypeController;
use App\Http\Controllers\CertificateRequestController;
use App\Http\Controllers\IssuedCertificateController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CodeStandardController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\ActorController;

/*
|--------------------------------------------------------------------------
| Authenticated User
|--------------------------------------------------------------------------
*/

Route::get('/me', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'can_add_article' => $user->can_add_article,
        ]
    ]);
})->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| Tags
|--------------------------------------------------------------------------
*/

Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{id}', [TagController::class, 'show']);

Route::post('/tags', [TagController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::put('/tags/{id}', [TagController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::delete('/tags/{id}', [TagController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can:manage-content']);


/*
|--------------------------------------------------------------------------
| Articles
|--------------------------------------------------------------------------
*/

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

Route::post('/articles', [ArticleController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::put('/articles/{id}', [ArticleController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can:manage-content']);


/*
|--------------------------------------------------------------------------
| Magazines
|--------------------------------------------------------------------------
*/

Route::get('/magazines', [MagazineController::class, 'index']);
Route::get('/magazines/{id}', [MagazineController::class, 'show']);

Route::post('/magazines', [MagazineController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::put('/magazines/{id}', [MagazineController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::delete('/magazines/{id}', [MagazineController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can:manage-content']);


/*
|--------------------------------------------------------------------------
| Booklets
|--------------------------------------------------------------------------
*/

Route::get('/booklets', [BookletController::class, 'index']);
Route::get('/booklets/{id}', [BookletController::class, 'show']);

Route::post('/booklets', [BookletController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::put('/booklets/{id}', [BookletController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::delete('/booklets/{id}', [BookletController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can:manage-content']);



/*
|--------------------------------------------------------------------------
| Codes & Standards
|--------------------------------------------------------------------------
*/

Route::get('/code-standards', [CodeStandardController::class, 'index']);

Route::get('/code-standards/{id}', [CodeStandardController::class, 'show']);

Route::post('/code-standards', [CodeStandardController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::put('/code-standards/{id}', [CodeStandardController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::delete('/code-standards/{id}', [CodeStandardController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'can:manage-content']);



Route::get('/certificate-types', [CertificateTypeController::class, 'index']);

Route::post('/certificate-types', [CertificateTypeController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::get('/certificate-types/{id}', [CertificateTypeController::class, 'show']);

Route::put('/certificate-types/{id}', [CertificateTypeController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-content']);


Route::post(
    '/certificate-requests',
    [CertificateRequestController::class, 'store']
)->middleware('throttle:5,1');

Route::get('/certificate-requests', [CertificateRequestController::class, 'index'])
    ->middleware(['auth:sanctum', 'can:manage-content']);
Route::get('/certificate-requests/{id}', [CertificateRequestController::class, 'show'])
    ->middleware(['auth:sanctum', 'can:manage-content']);
Route::patch('/certificate-requests/{id}/status', [CertificateRequestController::class, 'updateStatus'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::patch(
    '/certificate-requests/{id}/confirm-payment',
    [CertificateRequestController::class, 'confirmPayment']
)->middleware(['auth:sanctum', 'can:manage-content']);

Route::post(
    '/certificate-requests/{id}/issue-certificate',
    [IssuedCertificateController::class, 'store']
)->middleware(['auth:sanctum', 'can:manage-content']);

Route::get(
    '/issued-certificates',
    [IssuedCertificateController::class, 'index']
)->middleware(['auth:sanctum', 'can:manage-content']);

Route::get(
    '/issued-certificates/{id}',
    [IssuedCertificateController::class, 'show']
)->middleware(['auth:sanctum', 'can:manage-content']);

Route::get(
    '/certificates/verify/{code}',
    [IssuedCertificateController::class, 'verify']
);

Route::put(
    '/issued-certificates/{id}',
    [IssuedCertificateController::class, 'update']
)->middleware(['auth:sanctum', 'can:manage-content']);

Route::get(
    '/admin/certificate-types',
    [CertificateTypeController::class, 'adminIndex']
)->middleware(['auth:sanctum', 'can:manage-content']);

Route::post('/articles/{article}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:5,1');



Route::get('/articles/{article}/comments', [CommentController::class, 'index'])
    ->middleware('throttle:60,1');


Route::get('/admin/comments', [CommentController::class, 'adminIndex'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::patch('/admin/comments/{comment}/approve', [CommentController::class, 'approve'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::patch('/admin/comments/{comment}/reject', [CommentController::class, 'reject'])
    ->middleware(['auth:sanctum', 'can:manage-content']);

Route::post('/magazines/{magazine}/comments', [CommentController::class, 'storeMagazine'])
    ->middleware('throttle:5,1');

Route::get('/magazines/{magazine}/comments', [CommentController::class, 'indexMagazine'])
    ->middleware('throttle:60,1');
Route::post('/booklets/{booklet}/comments', [CommentController::class, 'storeBooklet'])
    ->middleware('throttle:5,1');

Route::get('/booklets/{booklet}/comments', [CommentController::class, 'indexBooklet'])
    ->middleware('throttle:60,1');
Route::post(
    '/code-standards/{codeStandard}/comments',
    [CommentController::class, 'storeCodeStandard']
)->middleware('throttle:5,1');

Route::get(
    '/code-standards/{codeStandard}/comments',
    [CommentController::class, 'indexCodeStandard']
)->middleware('throttle:60,1');

// Public Conference Routes
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);

// Admin Conference Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/conferences', [ConferenceController::class, 'store']);

    Route::put('/conferences/{conference}', [ConferenceController::class, 'update']);
    Route::patch('/conferences/{conference}', [ConferenceController::class, 'update']);

    Route::delete('/conferences/{conference}', [ConferenceController::class, 'destroy']);
});
Route::post('/conferences/{conference}/comments', [CommentController::class, 'storeConference']);

Route::get('/conferences/{conference}/comments', [CommentController::class, 'indexConference']);

// Public Competition Routes
Route::get('/competitions', [CompetitionController::class, 'index']);
Route::get('/competitions/{competition}', [CompetitionController::class, 'show']);

// Public Competition Comments
Route::post('/competitions/{competition}/comments', [CommentController::class, 'storeCompetition']);
Route::get('/competitions/{competition}/comments', [CommentController::class, 'indexCompetition']);

// Admin Competition Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/competitions', [CompetitionController::class, 'store']);

    Route::put('/competitions/{competition}', [CompetitionController::class, 'update']);
    Route::patch('/competitions/{competition}', [CompetitionController::class, 'update']);

    Route::delete('/competitions/{competition}', [CompetitionController::class, 'destroy']);
});

// Public Actor Routes
Route::get('/actors', [ActorController::class, 'index']);
Route::get('/actors/{actor}', [ActorController::class, 'show']);

// Admin Actor Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/actors', [ActorController::class, 'store']);

    Route::put('/actors/{actor}', [ActorController::class, 'update']);
    Route::patch('/actors/{actor}', [ActorController::class, 'update']);

    Route::delete('/actors/{actor}', [ActorController::class, 'destroy']);
});