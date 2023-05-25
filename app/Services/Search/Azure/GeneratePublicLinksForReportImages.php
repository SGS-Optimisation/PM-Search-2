<?php


namespace App\Services\Search\Azure;


use App\Models\Interfaces\Searchable;
use App\Models\Search;

class GeneratePublicLinksForReportImages
{
    public Search $search;

    /**
     * ReportImageLinks constructor.
     * @param  Search  $search
     */
    public function __construct(Search $search)
    {
        $this->search = $search;
    }


    public function handle()
    {
        /*
         * Check if not already processed
         */
        if (isset($this->search->report['output'])
            && (!isset($this->search->working_data['image_linking'])
                || !$this->search->working_data['image_linking'])
        ) {

            $working_data = $this->search->working_data;
            $working_data['image_linking'] = false;

            $report = $this->search->report;


            if ($this->search->search_mode == Searchable::SEARCH_MODE_TEXT) {
                foreach ($report['output'] as &$entry) {
                    $this->processEntry($entry);
                }
            } elseif ($this->search->search_mode == Searchable::SEARCH_MODE_IMAGE) {
                foreach ($report['output'] as &$search_tech) {
                    foreach ($search_tech as &$entry) {
                        $this->processEntry($entry);
                    }
                }
            }

            $working_data['image_linking'] = true;

            $this->search->report = $report;
            $this->search->working_data = $working_data;

            $this->search->save();
        }
    }

    protected function processEntry(&$entry)
    {
        if (!isset($entry['file_name'])) {
            return;
        }

        try {
            $image_lrg = $entry['file_name'];
            $image_sml = str_replace('LRG', 'SML', $image_lrg);

            $entry['image_lrg'] = \Storage::disk('azure')->temporaryUrl($image_lrg, now()->addYear());
            $entry['image_sml'] = \Storage::disk('azure')->temporaryUrl($image_sml, now()->addYear());
        } catch (\Exception $e) {
            logger('error getting image in entry: ' . $e->getMessage());
        }
    }
}
