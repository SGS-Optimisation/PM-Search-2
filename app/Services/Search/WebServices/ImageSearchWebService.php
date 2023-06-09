<?php


namespace App\Services\Search\WebServices;


use App\Models\Interfaces\Searchable;
use App\Models\Search;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageSearchWebService
{
    /**
     * SearchApi constructor.
     * @param string|Searchable $filepath
     * @param array $search_techs
     */
    public function __construct(public string|Searchable $filepath, public array $search_techs = [])
    {
        if (!is_string($this->filepath)) {
            $search = $this->filepath;
            $this->filepath = $search->image_path;

            $this->search_techs = [];
            foreach (config('pm-search.image_search_techs') as $tech => $data) {
                $this->search_techs[$tech] = in_array($tech, $search->working_data['search_techs']) ? 'Y' : 'N';
            }
        }
    }


    public function handle()
    {
        $url = config('pm-search.image_search_url');

        $response = Http::timeout(60)
            ->attach('image_file', Storage::get($this->filepath), pathinfo($this->filepath, PATHINFO_BASENAME))
            ->attach('params', json_encode($this->search_techs), 'params.txt')
            ->post($url);

        return json_decode($response->body());
    }
}
