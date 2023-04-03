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
     * @param  string[]|Search  $search_string
     * @param  string|null  $operator
     * @param  bool|null  $isPhrase
     */
    public function __construct(
        public string|array|Search $search_string,
        public ?string $operator = 'and',
        public ?bool $isPhrase = true,
        public ?bool $isOCR = false,
        public ?string $type = TextSearchCreationService::TEXT_SEARCH_TYPE_THUMBNAIL,
    ) {
        if (!is_string($this->search_string)) {
            $search = $this->search_string;
            $this->search_string = $search->search_data->search_string;
            $this->operator = $search->search_data->operator;
            $this->isPhrase = $search->search_data->isPhrase;
            $this->isOCR = $search->search_data->isOCR;
            $this->type = $search->search_data->type;
        }
    }


    public function handle()
    {
        $url = config('pm-search.text_search_url');

        $data = [
            'textsearchstrings' => $this->search_string,
            'textsearchoperator' => $this->operator,
            'textsearchphrase' => $this->isPhrase,
            'ocr_output' => $this->isOCR,
            'type' => $this->type,
            'searchparameters' => ['textsearch' => 'Y', 'advanced_search' => 'N'],
        ];

        logger('text search with data: '. json_encode($data));

        $this->response = Http::post($url, $data);

        return json_decode($this->response->body());
    }
}
