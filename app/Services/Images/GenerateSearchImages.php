<?php


namespace App\Services\Images;


use App\Models\Interfaces\Searchable;
use App\Models\Search;
use App\Services\PDF\PdfToImage;
use Illuminate\Support\Facades\Storage;

class GenerateSearchImages
{
    protected false|string $source_filetype;

    public $start_time;
    public $end_time;


    /**
     * GenerateSearchImages constructor.
     * @param  Search  $search
     */
    public function __construct(public Search $search)
    {
        $this->source_filetype = Storage::mimeType($this->search->source_filepath);
    }

    public function handle()
    {
        $this->start_time = microtime(true);
        if ($this->source_filetype === 'image/jpeg'
            || $this->source_filetype === 'image/jpg'
            || $this->source_filetype === 'image/png'
        ) {
            /*
             * Already have image, move it
             */

            $image_path = sprintf("searches/%s/%s",
                $this->search->id, Searchable::SEARCH_SOURCE_FILENAME);
            Storage::move($this->search->source_filepath, $image_path);

            $this->search->image_path = $image_path;
            $working_data = $this->search->working_data;
            $working_data[Searchable::FLAG_HAS_SOURCE_IMAGE] = true;
            $this->search->working_data = $working_data;
            $this->search->save();
        } else {
            /*
             * Convert PDF to image
             */
            (new PdfToImage($this->search))->handle();
        }

        $this->makeThumb();
        $this->end_time = microtime(true);

        $working_data = $this->search->working_data;
        $working_data['generate_search_image_duration'] = round($this->end_time - $this->start_time, 2).'s';
        $this->search->working_data = $working_data;
        $this->search->save();
    }

    protected function makeThumb()
    {
        $thumb_path = sprintf("searches/%s/%s",
            $this->search->id, Searchable::SEARCH_THUMB_FILENAME);
        if (Storage::exists($thumb_path)) {
            Storage::delete($thumb_path);
        }
        if (Storage::copy($this->search->image_path, $thumb_path)) {
            $thumb = \Image::make(Storage::path($thumb_path))->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumb->save(Storage::path($thumb_path));

            $working_data = $this->search->working_data;
            $working_data['thumb'] = $thumb_path;

            $this->search->working_data = $working_data;
            $this->search->save();
        }
    }
}
