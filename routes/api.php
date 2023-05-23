<?php

use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::name('api.')
    ->middleware([
        'auth:sanctum',
        //'verified',
        //'user.permissions',
        //'cache.headers:public;max_age=3600;etag',
    ])->group(function () {

        Route::get('/configs', function(){
            return [
                'fields_config' => config('pm-search.fields'),
                'bridge_fields' => config('pm-search.bridge_view_fields'),
                'grouped_fields' => config('pm-search.grouped_fields'),
                'results_row_fields' => config('pm-search.results_row_fields'),
                'table_fields' => config('pm-search.tables'),
                'autocomplete_suggester_url' => config('pm-search.autocomplete_suggester_url'),
                'advanced_search_fields' => config('pm-search.advanced_search'),
            ];
        })->name('configs');

        Route::post('/convert', [UploadController::class, 'convert'])->name('pdf-to-image');

    });


