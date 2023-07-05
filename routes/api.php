<?php

use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollectionController;
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

        Route::get('/', function(){
            return ['message' => 'Hello, this is not the API you are looking for.'];
        });

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


        Route::name('photon.')->prefix('/photon')
            ->group(function () {

            Route::get('/token', function(){
                \App\Services\Photon\Api\AuthApi::appAuth();

                return [
                    'token' => Cache::get('photon_token'),
                    'expiry' => \Carbon\Carbon::parse(Cache::get('photon_token_expiry'))->toDateTimeString(),
                    'subscription' => nova_get_setting('photon_subscription_key'),
                    'host' => nova_get_setting('photon_api_base_path'),
                ];
            })->name('token');

            Route::get('job-colours/{jobNumber}', function($jobNumber){
                return \App\Services\Photon\Api\JobApi::jobColours($jobNumber);
            })->name('job-colours');
        });

        Route::post('/convert', [UploadController::class, 'convert'])->name('pdf-to-image');

        Route::name('collections.')->prefix('/collections')
            ->group(function() {

                Route::post('/', [CollectionController::class, 'create'])->name('create');
                Route::post('/from-search/{id}', [CollectionController::class, 'createFromSearch'])->name('create-from-search');
                Route::post('/from-collection/{id}', [CollectionController::class, 'createFromCollection'])->name('create-from-collection');
                Route::post('/{collection}/updateFilters', [CollectionController::class, 'updateFilters'])->name('update-filters');

                Route::get('/{collection}/fresh', [CollectionController::class, 'fresh'])->name('fresh');
            });


    });


