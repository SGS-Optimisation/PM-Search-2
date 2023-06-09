<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Search;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function index(Request $request)
    {
        $latest_searches = $request->user()->searches()
            ->latest()->take(40)
            ->select(['id', 'search_mode', 'search_data', 'working_data'])
            ->get();

        $collections = $request->user()->collections()
            ->latest()->take(40)
            ->select(['id', 'name', 'search_mode', 'search_data', 'working_data', 'created_at'])
            ->get();

        return Jetstream::inertia()->render($request, 'Dashboard', [
            'latestSearches' => $latest_searches,
            'collections' => $collections,
        ]);
    }
}
