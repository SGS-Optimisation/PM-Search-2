<?php

namespace App\Http\Controllers;

use App\Exports\SearchReportExport;
use App\Models\Search;
use App\Operations\PrepareSearchReportOperation;
use App\Services\Search\ImageSearchCreationService;
use App\Services\Search\TextSearchCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Maatwebsite\Excel\Facades\Excel;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function image(Request $request)
    {
        return Jetstream::inertia()->render($request, 'Search/ImageSearch', []);
    }

    public function text(Request $request)
    {
        return Jetstream::inertia()->render($request, 'Search/TextSearch', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        logger(print_r($request->all(), true));
        $tscs = new TextSearchCreationService(
            $request->search_string,
            $request->get('operator', 'and'),
            $request->get('isPhrase', true),
            $request->get('isOCR', false),
            $request->get('type', TextSearchCreationService::TEXT_SEARCH_TYPE_THUMBNAIL),
            $request->user());
        $tscs->handle();
        $tscs->getReport();

        return redirect(route('search.pending', [$tscs->search->id]));
    }

    public function refine(Request $request, Search $search)
    {
        return Jetstream::inertia()->render($request, 'Search/Refine', [
            'search' => $search,
            'filename' => $search->working_data['original_filename'] ?? '',
            'image_path' => url('/storage/'.$search->image_path),
            'thumb_path' => url('/storage/'.$search->working_data['thumb']),
        ]);
    }

    public function status(Request $request, Search $search)
    {
        return response()->json([
            'processed' => $search->working_data[Search::FLAG_PROCESSED] ?? false,
            'error' => $search->working_data[Search::FLAG_ERROR] ?? false,
        ]);
    }


    public function pending(Request $request, Search $search)
    {
        if ($search->working_data[Search::FLAG_PROCESSED]) {
            return redirect(route('search.show', [$search->id]));
        }

        return Jetstream::inertia()->render($request, 'Search/Pending', [
            'search' => $search,
            'filename' => $search->working_data['original_filename'] ?? '',
        ]);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Search  $search
     * @return \Inertia\Response
     */
    public function show(Request $request, Search $search)
    {
        $report_preparor = (new PrepareSearchReportOperation($search))->handle();

        $meta = [];
        if($search->search_mode == Search::SEARCH_MODE_IMAGE) {
            $meta['image_processing_duration'] = $search->working_data['generate_search_image_duration'] ?? null;
        }

        $meta['webservice_response_time'] = $search->working_data[$search->search_mode .'_webservice_duration'] ?? null;


        return Jetstream::inertia()->render($request, 'Search/Show', [
            'mode' => $search->search_mode,
            'search_data' => $search->search_data,
            'thumb' => url('/storage/'.($search->working_data['thumb'] ?? 'none.jpg')),
            'filename' => $search->working_data['original_filename'] ?? "[Sir/Ma'am, this is a Text Search]",
            'report' => $report_preparor->report_output,
            'fields' => $report_preparor->fields,
            'image_path' => url('/storage/'.$search->image_path),
            'working_data' => $search->working_data,
            'fields_config' => config('pm-search.fields'),
            'search_id' => $search->id,
            'parent_search_id' => $search->parent_id,
            'tag_ranking' => collect(config('pm-search.image_search_techs'))
                ->map(function($item, $key){return $item['position'];})->toArray(),
            'meta' => $meta,
        ]);
    }

    public function download(Request $request, Search $search)
    {
        return Excel::download(new SearchReportExport($search), $search->title.'.xlsx');
    }
}
