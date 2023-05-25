<?php


namespace App\Services\Search;


use App\Jobs\ImageSearchJob;
use App\Jobs\SearchWebserviceJob;
use App\Models\Interfaces\Searchable;
use App\Models\Search;
use App\Models\User;
use App\Services\PDF\PdfToImage;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class TextSearchCreationService
{

    public ?Search $search;

    const TEXT_SEARCH_TYPE_THUMBNAIL = 'thumbnail';
    const TEXT_SEARCH_TYPE_MYSGS = 'mysgs';

    protected string $source_filetype;

    protected array $advanced_fields;

    protected array $search_strings;

    /**
     * TextSearchCreationService constructor.
     * @param string[]|string $input_string
     * @param string $operator
     * @param bool $isPhrase
     * @param User|null $user
     */
    public function __construct(
        public array|string $input_string,
        public string       $operator = 'and',
        public ?User        $user = null,
    )
    {
        $this->advanced_fields = [];
        $this->search_strings = [];
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

        $this->extractAdvancedFields();

        $search_data = [
            'textsearchstrings' => $this->search_strings,
            'textsearchoperator' => $this->operator,
            'searchparameters' => ['textsearch' => 'Y', 'advanced_search' => count($this->advanced_fields) > 0 ? 'Y' : 'N'],
        ];

        if(count($this->advanced_fields) > 0) {
            $search_data['fields'] = $this->advanced_fields;
        }

        $working_data = [
            Searchable::FLAG_PROCESSED => false,
            Searchable::FLAG_HAS_SOURCE_IMAGE => false,
        ];

        $search = Search::create([
            'search_mode' => Searchable::SEARCH_MODE_TEXT,
            'user_id' => optional($this->user)->id,
            'search_data' => $search_data,
            'working_data' => $working_data
        ]);

        $this->search = $search;
    }

    protected function extractAdvancedFields()
    {
        $query = $this->input_string;

        if (!is_array($query)) {
            $query = explode(',', $query);
        }

        foreach ($query as $segment) {
            $segment = trim($segment);
            if (preg_match('/^(\w+):(.*)$/', $segment, $matches)) {

                logger('matches: '. json_encode($matches));

                $field_config = Arr::where(config('pm-search.advanced_search'), function ($config, $key) use ($matches) {
                    return $config['key'] === $matches[1];
                });
                $field = array_key_first($field_config);

                $value = $matches[2];

                if (config('pm-search.advanced_search')[$field]['type'] === 'date') {
                    $dates = explode('>', $value);
                    $value = json_encode([
                        Carbon::parse($dates[0])->toISOString(),
                        Carbon::parse($dates[1])->toISOString(),
                    ]);
                }

                $this->advanced_fields[$field] = $value;

            } else {
                $this->search_strings[] = $segment;
            }
        }
    }


}
