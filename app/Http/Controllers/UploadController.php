<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDocumentRequest;
use App\Models\Search;
use App\Services\Search\ImageSearchCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Jetstream;
use Spatie\PdfToImage\Pdf;

class UploadController extends Controller
{
    /**
     * @param UploadDocumentRequest $request
     * @param Search|null $search
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(UploadDocumentRequest $request, Search $search = null)
    {
        $document_path = Storage::putFile('documents', $request->file('document'));
        logger('saved doc to ' . $document_path);

        $filename = $request->file('document')->getClientOriginalName();
        $search_techs = $request->search_techs;


        $iscs = new ImageSearchCreationService($document_path, $filename, $search_techs, $request->user(), $search);
        $iscs->handle();
        $iscs->getReport();

        //dd($document_path, $filename, $search_techs);

        return redirect(route('search.pending', [$iscs->search->id]));
    }

    public function convert(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:pdf',
        ]);

        Storage::makeDirectory( 'conversion');
        Storage::makeDirectory( 'conversion/output');

        $path = Storage::putFileAs(
            'conversion',
            $request->file('document'),
            time() . '_' . $request->file('document')->getClientOriginalName()
        );

        $pdf = new Pdf(Storage::path($path));

        $path = 'conversion/output/' . uuid_create() . '.jpg';
        $pdf->saveImage(Storage::path($path));

        return response()->json(['path' => Storage::url($path)]);
    }
}
