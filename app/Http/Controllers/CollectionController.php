<?php

namespace App\Http\Controllers;

use App\Jobs\SearchWebserviceJob;
use App\Models\Collection;
use App\Models\Interfaces\Searchable;
use App\Models\Search;
use App\Operations\PrepareSearchReportOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Jetstream;

class CollectionController extends Controller
{

    public function index(Request $request)
    {
        $collections = Collection::where('user_id', $request->user()->id)
            ->select(['id','name', 'search_mode', 'search_data', 'source_filepath', 'image_path', 'working_data'])->get();

        return Jetstream::inertia()->render($request, 'Collection/Index', [
            'collections' => $collections,
        ]);
    }

    public function create(Request $request)
    {

    }

    public function createFromSearch(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            //'id' => 'required|integer|exists:searches,id',
        ]);

        $search = Search::findOrFail($id);

        $collection = Collection::create([
            'name' => $request->name,
            'search_mode' => $search->search_mode,
            'user_id' => $search->user_id,
            'search_data' => $search->search_data,
            'source_filepath' => $search->source_filepath,
            'image_path' => $search->image_path,
            'working_data' => $search->working_data,
            'report' => $search->report,
        ]);

        return redirect(route('collections.show', [$collection->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param Collection $collection
     * @return \Inertia\Response
     */
    public function show(Request $request, Collection $collection)
    {
        $collection->timestamps = false;
        $collection->update(['consulted_at' => Carbon::now()]);

        $report_preparator = (new PrepareSearchReportOperation($collection))->handle();

        $meta = [];
        if ($collection->search_mode == Searchable::SEARCH_MODE_IMAGE) {
            $meta['image_processing_duration'] = $collection->working_data['generate_search_image_duration'] ?? null;
        }

        $meta['webservice_response_time'] = $collection->working_data[$collection->search_mode . '_webservice_duration'] ?? null;


        return Jetstream::inertia()->render($request, 'Collection/Show', [
            'collection_id' => $collection->id,
            'collection_name' => $collection->name,
            'mode' => $collection->search_mode,
            'search_data' => $collection->search_data,
            'thumb' => Storage::url($collection->working_data['thumb'] ?? 'none.jpg'),
            'filename' => $collection->working_data['original_filename'] ?? "[Sir/Ma'am, this is a Text Search]",
            'report' => $report_preparator->report_output,
            'fields' => $report_preparator->fields,
            'image_path' => Storage::url($collection->image_path),
            'working_data' => $collection->working_data,
            'fields_config' => config('pm-search.fields'),
            'tag_ranking' => collect(config('pm-search.image_search_techs'))
                ->map(function ($item, $key) {
                    return $item['position'];
                })->toArray(),
            'meta' => $meta,
        ]);
    }

    public function fresh(Request $request, Collection $collection)
    {
        if ($collection->updated_at->diffInMinutes() < 30) {
            return response()->json(['message' => 'Collection is fresh', 'already_fresh' => true], 200);
        } else {

            $collection->working_data = [];
            $collection->save();

            dispatch_sync(new SearchWebserviceJob($collection, $collection->search_mode));

            $collection = $collection->fresh();

            $report_preparator = (new PrepareSearchReportOperation($collection))->handle();

            return response()->json([
                'message' => 'Collection refreshed',
                'already_fresh' => false,
                'report' => $report_preparator->report_output,
            ], 200);

        }
    }

    public function update(Request $request, Collection $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $collection->update($request->all());

        return back(303);
    }

    public function delete(Request $request, Collection $collection)
    {
        $collection->delete();

        return redirect(route('collections.index'));
    }
}
