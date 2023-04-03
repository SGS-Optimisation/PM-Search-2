<?php

namespace App\Jobs;

use App\Models\Search;
use App\Services\Search\Azure\GeneratePublicLinksForReportImages;
use App\Services\Search\WebServices\ImageSearchWebService;
use App\Services\Search\WebServices\TextSearchWebService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SearchWebserviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const SEARCH_MODE_IMAGE = 'image';
    const SEARCH_MODE_TEXT = 'text';

    public $start_time;
    public $end_time;

    /**
     * Create a new job instance.
     *
     * @param  Search  $search
     * @param  string  $mode
     */
    public function __construct(public Search $search, public string $mode = self::SEARCH_MODE_TEXT)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->start_time = microtime(true);

        $webservice = match ($this->mode) {
            self::SEARCH_MODE_IMAGE => ImageSearchWebService::class,
            default => TextSearchWebService::class,
        };

        $result = (new $webservice($this->search))->handle();

        if (!isset($result->output) || empty($result->output)) {
            $this->fail(new \Exception("No data from API"));
        }

        $this->end_time = microtime(true);

        $this->search->report = $result;
        $this->search->save();

        (new GeneratePublicLinksForReportImages($this->search))->handle();

        $working_data = $this->search->working_data;
        $working_data[Search::FLAG_PROCESSED] = true;
        $working_data[$this->mode. '_webservice_duration'] = round($this->end_time - $this->start_time, 2) . 's';
        $this->search->working_data = $working_data;
        $this->search->save();
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        $working_data = $this->search->working_data;
        $working_data[Search::FLAG_ERROR] = true;
        $this->search->working_data = $working_data;
        $this->search->save();
    }
}
