<?php


namespace App\Services\Search\WebServices;


use App\Models\Search;
use App\Services\Search\TextSearchCreationService;
use Illuminate\Support\Facades\Http;

class TextSearchWebService
{
    public $response;


    /**
     * TextSearchWebService constructor.
     * @param string[]|Search $search_string
     * @param string|null $operator
     */
    public function __construct(
        public string|array|Search $search_string,
        public ?string             $operator = 'and',
        public array               $params = ['textsearch' => 'Y', 'advanced_search' => 'N'],
        public array               $fields = [],
    )
    {
    }


    public function handle()
    {
        $url = config('pm-search.text_search_url');

        if ($this->search_string instanceof Search) {
            $data = $this->search_string->search_data;
        } else {
            $data = [
                'textsearchstrings' => $this->search_string,
                'textsearchoperator' => $this->operator,
                'searchparameters' => ['textsearch' => 'Y', 'advanced_search' => 'N'],
            ];

            if (count($this->fields) > 0) {
                $data['searchparameters']['advanced_search'] = 'Y';
                $data['searchparameters']['fields'] = $this->fields;
            }
        }

        logger('text search with data: ' . json_encode($data));

        $this->response = Http::timeout(60)->post($url, $data);

        return json_decode($this->response->body());
    }
}
