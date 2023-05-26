<?php


namespace App\Operations;


use App\Models\Interfaces\Searchable;
use App\Models\Search;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PrepareSearchReportOperation extends BaseOperation
{
    public array $report_output;
    public array $fields;
    public array $headers;

    public function __construct(public Searchable $search)
    {
    }

    public function handle()
    {
        logger('PrepareSearchReportOperation', [
            'search' => $this->search->toArray(),
        ]);

        $this->report_output = match ($this->search->search_mode) {
            Searchable::SEARCH_MODE_TEXT => $this->search->report['output'] ?? [],
            Searchable::SEARCH_MODE_IMAGE => static::merge_outputs($this->search->report['output'] ?? []),
        };

        /*
         * Build fields, used in filtering
         */
        $this->fields = [];
        foreach ($this->report_output as &$entry) {
            $this->extractCustomersAndPrinters($entry);

            foreach ($entry as $field => $value) {
                $sc_field = Str::snake($field);

                if (in_array($sc_field, array_keys(config('pm-search.fields')))
                    && config('pm-search.fields')[$sc_field]['search']
                ) {
                    if (!isset($this->fields[$sc_field])) {
                        $this->fields[$sc_field] = [];
                    }

                    $value_type = config('pm-search.fields')[$sc_field]['response_type'];

                    switch ($value_type) {
                        case 'list':
                            //foreach($value as $v) {
                            $this->fields[$sc_field][] = $value;
                            //}
                            break;
                        case 'str':
                        default:
                            $this->fields[$sc_field][] = $value;
                            break;
                    }
                }
            }
        }

        /*
         * Build headers, used in table headers
         */
        $this->headers = array_keys(config('pm-search.fields'));
        foreach ($this->fields as $field => $list) {
            $this->fields[$field] = array_values(collect($list)->unique()->toArray());
        }

        return $this;
    }

    protected function extractCustomersAndPrinters(&$entry)
    {
        // customer_name should be split into customer_name_only and printer_name, using customer_type to determine which is which
        $customer_names = $entry['customer_name'];
        $customer_types = $entry['customer_type'];

        if(count ($customer_names) != count($customer_types)){
            logger('error in entry ' . print_r($entry, true));
            return;
        }

        try{
            $combined = array_combine($customer_names, $customer_types);
        }catch(\Exception $e){
            dd($entry);
        }


        $printers = array_keys(Arr::where($combined, function ($value, $key) {
            return $value == "Printer";
        }));
        $customers = array_keys(Arr::where($combined, function ($value, $key) {
            return $value == "End User";
        }));

        $entry['customer_name_only'] = $customers;
        $entry['printer_name'] = $printers;
    }


    protected static function merge_outputs($output)
    {
        $all_entries = collect();
        foreach ($output as $search_tech => $entries) {
            if (Str::endsWith($search_tech, 'results')) {
                $all_entries = $all_entries->merge($entries);
            }
        }
        return $all_entries->toArray();
    }

}
