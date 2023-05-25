<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Interfaces\Searchable;
use App\Models\Search;
use App\Operations\PrepareSearchReportOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Jetstream;

class CollectionController extends Controller
{
    public function create(Request $request)
    {

    }

    public function createFromSearch(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id' => 'required|integer|exists:searches,id',
        ]);

        $search = Search::find($id);

        $collection = Collection::create([
            'search_mode' => $search->search_mode,
            'user_id' => $search->user_id,
            'search_data' => $search->search_data,
            'source_filepath' => $search->source_filepath,
            'image_path' => $search->image_path,
            'working_data' => $search->working_data,
            'report' => $search->report,
        ]);

        return $collection->id;
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
        if($search->search_mode == Searchable::SEARCH_MODE_IMAGE) {
            $meta['image_processing_duration'] = $search->working_data['generate_search_image_duration'] ?? null;
        }

        $meta['webservice_response_time'] = $search->working_data[$search->search_mode .'_webservice_duration'] ?? null;


        return Jetstream::inertia()->render($request, 'Search/Show', [
            'mode' => $search->search_mode,
            'search_data' => $search->search_data,
            'thumb' => Storage::url($search->working_data['thumb'] ?? 'none.jpg'),
            'filename' => $search->working_data['original_filename'] ?? "[Sir/Ma'am, this is a Text Search]",
            'report' => $report_preparor->report_output,
            'fields' => $report_preparor->fields,
            'image_path' => Storage::url($search->image_path),
            'working_data' => $search->working_data,
            'fields_config' => config('pm-search.fields'),
            'search_id' => $search->id,
            'parent_search_id' => $search->parent_id,
            'tag_ranking' => collect(config('pm-search.image_search_techs'))
                ->map(function($item, $key){return $item['position'];})->toArray(),
            'meta' => $meta,
        ]);
    }
}
