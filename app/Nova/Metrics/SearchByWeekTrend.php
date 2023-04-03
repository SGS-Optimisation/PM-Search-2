<?php

namespace App\Nova\Metrics;

use App\Models\Search;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class SearchByWeekTrend extends Trend
{
    /**
     * SearchTrend constructor.
     */
    public function __construct(public ?string $userId = null)
    {
        parent::__construct();
    }


    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->countByWeeks(
            $request,
            Search::query()->when($this->userId, function ($query) {
                return $query->where('user_id', $this->userId);
            }));
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            4 => __('4 Weeks'),
            10 => __('10 Weeks'),
            52 => __('1 Year'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'search-trend';
    }
}
