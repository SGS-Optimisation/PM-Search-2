<?php


namespace App\Services\Search;


use App\Jobs\ImageSearchJob;
use App\Jobs\SearchWebserviceJob;
use App\Models\Search;
use App\Models\User;
use App\Services\PDF\PdfToImage;
use Illuminate\Support\Facades\Storage;

class TextSearchCreationService
{

    public ?Search $search;

    const TEXT_SEARCH_TYPE_THUMBNAIL = 'thumbnail';
    const TEXT_SEARCH_TYPE_MYSGS = 'mysgs';

    protected string $source_filetype;

    /**
     * TextSearchCreationService constructor.
     * @param  string[]|string  $search_string
     * @param  string  $operator
     * @param  bool  $isPhrase
     * @param  User|null  $user
     */
    public function __construct(
        public array|string $search_string,
        public string $operator = 'and',
        public bool $isPhrase = true,
        public bool $isOCR = false,
        public string $type = self::TEXT_SEARCH_TYPE_THUMBNAIL,
        public ?User $user = null,
    ) {
    }

    public function handle()
    {
        $this->setup();
    }

    public function getReport($async = true)
    {
        $job = new SearchWebserviceJob($this->search, SearchWebserviceJob::SEARCH_MODE_TEXT);
        $async ? dispatch($job) : dispatch_sync($job);
    }

    protected function setup()
    {
        if (isset($this->search)) {
            return;
        }

        $search_data = [
            'search_string' => is_array($this->search_string) ? $this->search_string : explode(',', $this->search_string),
            'operator' => $this->operator,
            'isPhrase' => $this->isPhrase,
            'isOCR' => $this->isOCR,
            'type' => $this->type,
        ];

        $working_data = [
            Search::FLAG_PROCESSED => false,
            Search::FLAG_HAS_SOURCE_IMAGE => false,
        ];

        $search = Search::create([
            'search_mode' => Search::SEARCH_MODE_TEXT,
            'user_id' => optional($this->user)->id,
            'search_data' => $search_data,
            'working_data' => $working_data
        ]);

        $this->search = $search;
    }



}
