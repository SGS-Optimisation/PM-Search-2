<?php


namespace App\Services\PDF;


use App\Models\Search;
use Spatie\PdfToImage\Pdf;
use Storage;

class PdfToImage
{
    public Search $search;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $working_data = $this->search->working_data;

        /*
         * Make Source image
         */
        $pdf = new Pdf(Storage::path($this->search->source_filepath));
        Storage::makeDirectory( sprintf("searches/%s", $this->search->id));
        $image_path = sprintf("searches/%s/%s",
            $this->search->id, Search::SEARCH_SOURCE_FILENAME);
        $pdf->saveImage(Storage::path($image_path));
        $working_data['source_image'] = $image_path;
        $working_data[Search::FLAG_HAS_SOURCE_IMAGE] = true;


        /*
         * Make thumb
         */
        $thumb_path = sprintf("searches/%s/%s",
            $this->search->id, Search::SEARCH_THUMB_FILENAME);
        if(Storage::exists($thumb_path)){
           Storage::delete($thumb_path);
        }
        if(Storage::copy($image_path, $thumb_path)){
            $thumb = \Image::make(Storage::path($thumb_path))->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumb->save(Storage::path($thumb_path));
            $working_data['thumb'] = $thumb_path;
        }

        /*
         * Update search model
         */
        $this->search->working_data = $working_data;
        $this->search->image_path = $image_path;
        $this->search->save();
    }
}
