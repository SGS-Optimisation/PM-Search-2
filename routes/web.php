<?php

use App\Http\Controllers\Auth\AzureAuthController;
use App\Http\Controllers\CaddyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UploadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/login/azure/callback', [AzureAuthController::class, 'handleOauthResponse'])
    ->middleware(['web']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', function () {
        return redirect(route('dashboard'));
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/upload', [UploadController::class, 'store'])->name('image-search');

    Route::name('search.')->prefix('/search')
        ->group(function () {
            Route::post('/', [SearchController::class, 'store'])->name('store');

            Route::get('/image', [SearchController::class, 'image'])->name('image');
            Route::get('/text', [SearchController::class, 'text'])->name('text');

            Route::prefix('/{search}')->group(function ($group) {
                Route::get('/', [SearchController::class, 'show'])->name('show');
                Route::get('/status', [SearchController::class, 'status'])->name('status');
                Route::get('/pending', [SearchController::class, 'pending'])->name('pending');

                Route::get('/refine', [SearchController::class, 'refine'])->name('refine');
                Route::post('refine', [UploadController::class, 'store'])->name('refine.store');
                Route::get('download', [SearchController::class, 'download'])->name('download');

                /*foreach($group->getRoutes() as $route){
                    $route->where('search', '[0-9a-Z]+');
                }*/

            });
        });
});
