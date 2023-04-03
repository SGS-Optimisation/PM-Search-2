<?php


namespace App\Services\Search;


use App\Jobs\GenerateSearchImagesJob;
use App\Jobs\SearchWebserviceJob;
use App\Models\Search;
use App\Models\User;
use App\Services\PDF\PdfToImage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class ImageSearchCreationService
{
    public ?Search $search;

    protected string $source_filetype;

    /**
     * SearchCreationService constructor.
     * @param  string  $filepath
     * @param  string  $original_filename
     * @param  array  $search_techs
     * @param  User|null  $user
     * @param  int|Search|null  $parent_search
     */
    public function __construct(
        public string $filepath,
        public string $original_filename,
        public array $search_techs,
        public ?User $user,
        public int|Search|null $parent_search = null
    ) {

        $this->source_filetype = Storage::mimeType($this->filepath);

        if(is_int($parent_search)) {
            $this->parent_search = Search::find($parent_search);
        }
    }

    public function handle()
    {
        $this->setup();
    }

    public function getReport()
    {
        $job = new SearchWebserviceJob($this->search, SearchWebserviceJob::SEARCH_MODE_IMAGE);
        //$async ? dispatch($job) : dispatch_sync($job);

        Bus::chain([
            new GenerateSearchImagesJob($this->search),
            $job
        ])->dispatch();
    }

    protected function setup()
    {
        if (isset($this->search)) {
            return;
        }

        $working_data = [
            'original_filename' => $this->original_filename,
            Search::FLAG_PROCESSED => false,
            Search::FLAG_HAS_SOURCE_IMAGE => false,
            'search_techs' => $this->search_techs,
        ];

        $search = Search::create([
            'search_mode' => Search::SEARCH_MODE_IMAGE,
            'user_id' => optional($this->user)->id,
            'parent_id' => optional($this->parent_search)->id,
            'source_filepath' => $this->filepath,
            'working_data' => $working_data
        ]);

        $this->search = $search;
    }

}
