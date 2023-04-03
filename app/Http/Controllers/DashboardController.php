<?php

namespace App\Http\Controllers;

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
        $latest_searches = $request->user()->searches()->latest()->take(10)->get();

        return Jetstream::inertia()->render($request, 'Dashboard', [
            'latestSearches' => $latest_searches,
        ]);
    }
}
