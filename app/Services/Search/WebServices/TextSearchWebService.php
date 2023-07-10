<?php


namespace App\Services\Search\WebServices;


use App\Models\Interfaces\Searchable;
use App\Models\Search;
use App\Services\Search\TextSearchCreationService;
use Illuminate\Support\Facades\Http;

class TextSearchWebService
{
    public $response;


    /**
     * TextSearchWebService constructor.
     * @param string[]|Searchable $search_string
     * @param string|null $operator
     */
    public function __construct(
        public string|array|Searchable $search_string,
        public ?string                 $operator = 'and',
        public array                   $params = ['textsearch' => 'Y', 'advanced_search' => 'N'],
        public array                   $fields = [],
    )
    {
    }


    public function handle()
    {
        $url = config('pm-search.text_search_url');

        if ($this->search_string instanceof Searchable) {
            $data = $this->search_string->search_data;

            if (isset($data->fields->printer_name)) {
                $data->fields->customer_name = $data->fields->printer_name;
                $data->fields->customer_type = 'Printer';

                unset($data->fields->printer_name);
            }

        } else {
            $data = [
                'textsearchstrings' => $this->search_string,
                'textsearchoperator' => $this->operator,
                'searchparameters' => ['textsearch' => 'Y', 'advanced_search' => 'N'],
            ];

            if (count($this->fields) > 0) {
                $data['searchparameters']['advanced_search'] = 'Y';

                $parsed_fields = $this->fields;

                // custom behavior for printer name
                if (isset($parsed_fields['printer_name'])) {
                    logger('found printer name: ' . $parsed_fields['printer_name']);
                    $parsed_fields['customer_name'] = $parsed_fields['printer_name'];
                    $parsed_fields['customer_type'] = 'Printer';

                    unset($parsed_fields['printer_name']);
                }

                foreach ($parsed_fields as $term) {
                    $parsed_fields[] = $parsed_fields[$term];
                }

                $data['searchparameters']['fields'] = $parsed_fields;
            }
        }

        //logger('text search with data: ' . json_encode($data));

        $this->response = Http::timeout(60)->post($url, $data);

        return json_decode($this->response->body());
    }
}
